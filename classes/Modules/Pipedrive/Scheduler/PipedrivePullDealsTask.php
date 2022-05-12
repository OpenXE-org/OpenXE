<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Scheduler;

use ArrayObject;
use Exception;
use Xentral\Components\Database\Database;
use Xentral\Modules\Pipedrive\Exception\PipedriveConfigurationException;
use Xentral\Modules\Pipedrive\Exception\PipedriveDealServiceException;
use Xentral\Modules\Pipedrive\Exception\PipedriveMetaException;
use Xentral\Modules\Pipedrive\Gateway\PipedriveDealGateway;
use Xentral\Modules\Pipedrive\Service\PipedriveConfigurationService;
use Xentral\Modules\Pipedrive\Service\PipedriveDealService;
use Xentral\Modules\Pipedrive\Service\PipedriveEventService;
use Xentral\Modules\Pipedrive\Service\PipedriveMetaReaderService;
use Xentral\Modules\Pipedrive\Service\PipedriveMetaWriterService;
use Xentral\Modules\Pipedrive\Wrapper\PipedriveResubmissionWrapper;

final class PipedrivePullDealsTask implements PipedriveSchedulerTaskInterface
{
    /** @var Database $db */
    private $db;

    /** @var PipedriveMetaWriterService $metaWrite */
    private $metaWrite;

    /** @var PipedriveDealGateway $gateway */
    private $gateway;

    /** @var PipedriveDealService $dealService */
    private $dealService;

    /** @var PipedriveConfigurationService $configuration */
    private $configuration;

    /** @var PipedriveEventService $eventService */
    private $eventService;

    /** @var PipedriveMetaReaderService $metaReaderService */
    private $metaReaderService;

    /** @var PipedriveResubmissionWrapper $resubmissionWrapper */
    private $resubmissionWrapper;

    /**
     * @param PipedriveDealService          $dealService
     * @param Database                      $db
     * @param PipedriveMetaWriterService    $metaWriterService
     * @param PipedriveDealGateway          $gateway
     * @param PipedriveConfigurationService $configuration
     * @param PipedriveEventService         $eventService
     * @param PipedriveMetaReaderService    $metaReaderService
     * @param PipedriveResubmissionWrapper  $resubmissionWrapper
     */
    public function __construct(
        PipedriveDealService $dealService,
        Database $db,
        PipedriveMetaWriterService $metaWriterService,
        PipedriveDealGateway $gateway,
        PipedriveConfigurationService $configuration,
        PipedriveEventService $eventService,
        PipedriveMetaReaderService $metaReaderService,
        PipedriveResubmissionWrapper $resubmissionWrapper

    ) {
        $this->db = $db;
        $this->dealService = $dealService;
        $this->metaWrite = $metaWriterService;
        $this->gateway = $gateway;
        $this->configuration = $configuration;
        $this->eventService = $eventService;
        $this->metaReaderService = $metaReaderService;
        $this->resubmissionWrapper = $resubmissionWrapper;
    }

    /**
     * @param array       $option
     * @param string|null $type
     * @param bool        $recursiveMode
     *
     * @throws PipedriveConfigurationException
     * @throws PipedriveDealServiceException
     * @throws PipedriveMetaException
     *
     * @return void
     */
    public function execute(array $option = [], ?string $type = null, bool $recursiveMode = false): void
    {
        $settings = $this->configuration->getSettings();
        if ($settings['pd_sync_deals'] !== true) {
            return;
        }
        $type = $type ?? 'pipedrive_recently_updated_deals';
        if ($type !== 'pipedrive_recently_updated_deals' && empty($recursiveMode) &&
            $this->db->fetchValue('SELECT COUNT(id) FROM `pipedrive_deals` WHERE `hidden` = 0') > 0) {
            $type = 'pipedrive_recently_updated_deals';
        }
        $ret = $this->pull($type, $option);

        if (array_key_exists('has_more', $ret) && $ret['has_more'] === true) {
            $this->execute($option, $type, true);
        }
    }

    /**
     * @return void
     */
    public function cleanup(): void
    {
        // TODO: Implement cleanup() method.
    }

    /**
     * @param string $type
     * @param array  $options
     *
     * @throws PipedriveDealServiceException
     * @throws PipedriveMetaException
     * @throws Exception
     *
     * @return array
     */
    protected function pull(string $type = 'pipedrive_recently_updated_deals', array $options = []): array
    {
        $response = $this->dealService->pullDeals($type, $options);
        if ($response->getStatusCode() !== 200) {
            return [];
        }

        $deals = $response->getData();
        $pagination = $response->getPagination();
        $metaFile = sprintf('%s.json', $type);
        $metaOption = $this->metaReaderService->readFromFile($metaFile);
        $hasMore = is_array($pagination) && array_key_exists(
            'more_items_in_collection',
            $pagination
        ) ? $pagination['more_items_in_collection'] : false;

        $startOffset = 0;

        if (empty($metaOption)) {
            $timeOffset = '1970-01-01 23:59:59';
            $this->metaWrite->save($metaFile, ['timeOffset' => $timeOffset]);
        } elseif (array_key_exists('has_more', $options) && $options['has_more'] === true) {
            $timeOffset = $options['previous_timeOffset'] ?? '1970-01-01 23:59:59';
            $startOffset += 100;
        } else {
            $timeOffset = $metaOption['timeOffset'];
        }

        if (is_array($deals) && count($deals) > 0) {
            foreach ($deals as $deal) {
                $dealId = $deal['id'];
                $hDeal = $deal['data'];

                if ($hDeal['deleted'] === true) {
                    // DELETE IT HERE
                    if ($mapping = $this->gateway->getDealByPipedriveId($dealId)) {
                        $this->db->perform(
                            'DELETE FROM `pipedrive_deals` WHERE `pd_deal_id` = :id',
                            ['id' => $dealId]
                        );
                        $this->db->perform(
                            'DELETE FROM `wiedervorlage` WHERE id = :id',
                            ['id' => $mapping['wiedervorlage_id']]
                        );
                    }
                    continue;
                }

                if ($mapping = $this->gateway->getDealByPipedriveId($dealId)) {
                    $this->updateXTDeal($hDeal, $mapping['wiedervorlage_id']);
                } elseif ($pipelineId = $this->addXTDeal($hDeal)) {
                    $this->db->perform(
                        'INSERT INTO `pipedrive_deals` (`pd_deal_id`, `created_at`, `wiedervorlage_id`) 
                            VALUES (:id,NOW(), :pipelineId)',
                        ['id' => (int)$dealId, 'pipelineId' => $pipelineId]
                    );
                }
            }
        }

        if (!empty($metaOption) && !array_key_exists('previous_timeOffset', $metaOption)) {
            $this->metaWrite->save($metaFile, ['timeOffset' => gmdate('Y-m-d H:i:s')]);
        }

        return [
            'has_more'            => $hasMore,
            'previous_timeOffset' => $timeOffset,
            'startOffset'         => $startOffset,
        ];
    }

    /**
     * @param ArrayObject $data
     *
     * @throws PipedriveConfigurationException
     *
     * @return mixed|void
     */
    public function beforeScheduleAction(ArrayObject $data)
    {
        if (empty($this->configuration->tryGetConfiguration('pipedrive_settings'))) {
            return;
        }

        try {
            $leadsFields = $this->configuration->matchSelectedAddressFreeField();
        } catch (PipedriveConfigurationException $exception) {
            return;
        }

        if (empty($leadsFields)) {
            return;
        }
    }

    /**
     * @param ArrayObject $data
     */
    public function afterScheduleAction(ArrayObject $data)
    {
    }

    /**
     * @param array $deal
     *
     * @throws Exception
     *
     * @return int
     */
    private function addXTDeal(array $deal): int
    {
        $internalDeal = $this->configuration->formatDealToInternal($deal);
        if (!$internalDeal) {
            return 0;
        }

        $latestId = $this->resubmissionWrapper->addResubmission($internalDeal);
        if ($data = $this->gateway->getMappingStageByResubmissionStageId($internalDeal['stages'])) {
            $viewId = $data['wiedervorlage_view_id'];
            $eventMsg = sprintf(
                'Neues Deal (<a href="/index.php?module=wiedervorlage&action=list&view=%d">%s</a>) vom Pipedrive hinzugef&uuml;gt ins Xentral importiert',
                $viewId,
                $internalDeal['bezeichnung']
            );
            $this->eventService->add($eventMsg);
        }

        return $latestId;
    }

    /**
     * @param array $deal
     * @param int   $wvId
     *
     * @throws Exception
     *
     * @return void
     */
    private function updateXTDeal(array $deal, int $wvId): void
    {
        $internalDeal = $this->configuration->formatDealToInternal($deal);
        if (!$internalDeal) {
            return;
        }

        $this->resubmissionWrapper->updateResubmission($wvId, $internalDeal);

        if ($data = $this->gateway->getMappingStageByResubmissionStageId($internalDeal['stages'])) {
            $viewId = $data['wiedervorlage_view_id'];
            $eventMsg = sprintf(
                'Deal (<a href="/index.php?module=wiedervorlage&action=list&view=%d">%s</a>) 
                 vom Pipedrive ge&auml;ndert und ins Xentral importiert',
                $viewId,
                $internalDeal['bezeichnung']
            );
            $this->eventService->add($eventMsg);
        }
    }

}
