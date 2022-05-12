<?php

namespace Xentral\Modules\Hubspot\Scheduler;

use ArrayObject;
use Exception;
use Xentral\Components\Database\Database;
use Xentral\Modules\Hubspot\Exception\HubspotDealGatewayNotFoundException;
use Xentral\Modules\Hubspot\Exception\HubspotException;
use Xentral\Modules\Hubspot\HubspotEventService;
use Xentral\Modules\Hubspot\HubspotHttpResponseService as Response;
use Xentral\Modules\Hubspot\HubspotConfigurationService;
use Xentral\Modules\Hubspot\HubspotDealGateway;
use Xentral\Modules\Hubspot\HubspotDealService;
use Xentral\Modules\Hubspot\HubspotMetaService;
use Xentral\Modules\Hubspot\HubspotContactGateway;
use Xentral\Modules\SubscriptionCycle\Scheduler\TaskMutexServiceInterface;

final class HubspotPullDealsTask implements HubspotSchedulerTaskInterface
{
    /** @var Database $db */
    private $db;

    /** @var HubspotMetaService $meta */
    private $meta;

    /** @var HubspotDealGateway $gateway */
    private $gateway;

    /** @var HubspotDealService $dealService */
    private $dealService;

    /** @var HubspotConfigurationService $configuration */
    private $configuration;

    /** @var HubspotEventService $eventService */
    private $eventService;

    /** @var HubspotContactGateway $contactGateway */
    private $contactGateway;

    /** @var TaskMutexServiceInterface $taskMutexService */
    private $taskMutexService;

    /**
     * @param HubspotDealService          $dealService
     * @param Database                    $db
     * @param HubspotMetaService          $metaService
     * @param HubspotDealGateway          $gateway
     * @param HubspotConfigurationService $configuration
     * @param HubspotEventService         $eventService
     * @param HubspotContactGateway       $contactGateway
     * @param TaskMutexServiceInterface   $taskMutexService
     */
    public function __construct(
        HubspotDealService $dealService,
        Database $db,
        HubspotMetaService $metaService,
        HubspotDealGateway $gateway,
        HubspotConfigurationService $configuration,
        HubspotEventService $eventService,
        HubspotContactGateway $contactGateway,
        TaskMutexServiceInterface $taskMutexService
    ) {
        $this->db = $db;
        $this->dealService = $dealService;
        $this->meta = $metaService;
        $this->gateway = $gateway;
        $this->configuration = $configuration;
        $this->eventService = $eventService;
        $this->contactGateway = $contactGateway;
        $this->taskMutexService = $taskMutexService;
    }

    /**
     * @param array       $option
     *
     * @param null|string $type
     *
     * @param bool        $recursiveMode
     *
     * @throws Exception
     * @return void
     */
    public function execute($option = [], $type = null, $recursiveMode = false): void
    {
        if ($recursiveMode === false) {
            if ($this->taskMutexService->isTaskInstanceRunning('hubspot_pull_deals')) {
                return;
            }
            $this->taskMutexService->setMutex('hubspot_pull_deals');
        }
        $settings = $this->configuration->getSettings();
        if ($settings['hs_sync_deals'] !== true) {
            return;
        }

        $type = $type ?? 'all_deals';

        if ($type !== 'recently_updated_deals' && empty($recursiveMode) &&
            $this->db->fetchValue('SELECT COUNT(id) FROM hubspot_deals WHERE hidden=0') > 0) {
            $type = 'recently_created_deals';
        }

        $ret = $this->pull($type, $option);

        if (array_key_exists('has_more', $ret) && $ret['has_more'] === true) {
            $option = ['offset' => $ret['deal_offset']];
            $this->execute($option, $type, true);
        }

        if ($ret['has_more'] === false && !in_array($type, ['all_deals', 'recently_updated_deals'])) {
            $this->execute([], 'recently_updated_deals');
        }
    }

    /**
     * @return void
     */
    public function cleanup()
    {
        $this->taskMutexService->setMutex('hubspot_pull_deals', false);
    }

    /**
     * @param string $type
     * @param array  $options
     *
     * @throws Exception
     * @return array
     */
    protected function pull($type = 'recently_created_deals', $options = [])
    {
        /** @var Response $response */
        $response = $this->dealService->pullDeals($type, $options);

        if ($response->getStatusCode() === 200) {
            $data = $response->getJson();
            $offSet = array_key_exists('offset', $data) ? $data['offset'] : 0;
            $since = array_key_exists('since', $data) ? $data['since'] : 0;
            $hasMore = array_key_exists('hasMore', $data) ? $data['hasMore'] : false;

            if (array_key_exists('deals', $data) || array_key_exists('results', $data)) {
                $deals = array_key_exists('deals', $data) ? $data['deals'] : $data['results'];

                if (count($deals) > 0) {
                    foreach ($deals as $deal) {
                        $dealId = $deal['dealId'];
                        $addressId = 0;

                        $associations = array_key_exists('associations', $deal) ? $deal['associations'] : [];

                        if (!empty($associations)) {
                            $dealContactIds = $associations['associatedVids'];
                            $companyIds = $associations['associatedCompanyIds'];
                            if (!empty($companyIds)) {
                                $dealContactIds = $companyIds;
                            }
                            if (!empty($dealContactIds)) {
                                $dealContactId = $dealContactIds[0];
                                $hsContact = $this->contactGateway->getMappingByHubspotId($dealContactId, null);
                                if (!empty($hsContact)) {
                                    $addressId = $hsContact['address_id'];
                                }
                            }
                        }

                        if ($deal['isDeleted'] === true) {
                            // @todo delete from xentral
                            continue;
                        }
                        if ($mapping = $this->gateway->getByHubspotId($dealId)) {
                            // UPDATE
                            if (($type === 'recently_updated_deals')) {
                                $this->updateXTDeal($dealId, $mapping['wiedervorlage_id'], $addressId);
                                //$updated++;
                            }
                            continue;
                        }

                        if ($pipelineId = $this->addXTDeal($dealId, $addressId)) {
                            $this->db->perform(
                                'INSERT INTO `hubspot_deals` (`hs_deal_id`, `created_at`, `wiedervorlage_id`)
                                    VALUES (:id,NOW(), :pipelineId)',
                                ['id' => (int)$dealId, 'pipelineId' => $pipelineId]
                            );
                        }
                    }
                }
            }

            if ($since === 0) {
                $since = time() * 1000;
            }

            $this->meta->setName($type)->save(['offset' => $offSet, 'since' => $since]);

            return ['deal_offset' => $offSet, 'has_more' => $hasMore];
        }

        return [];
    }

    public function beforeScheduleAction(ArrayObject $data)
    {
        if (empty($this->configuration->tryGetConfiguration(HubspotConfigurationService::HUBSPOT_SALT_CONF_NAME))) {
            return;
        }

        try {
            $leadsFields = $this->configuration->matchSelectedAddressFreeField();
        } catch (HubspotException $exception) {
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
        // TODO: Implement afterSchedule() method.
    }

    /**
     * @param     $dealId
     * @param int $addressId
     *
     * @throws HubspotException
     * @throws HubspotDealGatewayNotFoundException
     *
     * @throws Exception
     * @return int
     */
    private function addXTDeal($dealId, $addressId = 0): int
    {
        if ($xtDeal = $this->configuration->formatDealByResponse($this->dealService->getDealById($dealId))) {
            $xtDeal['adr'] = $addressId;
            $this->db->perform(
                'INSERT INTO `wiedervorlage` (
                          `bezeichnung`,
                          `datum_angelegt`,
                          `zeit_angelegt`,
                          `datum_erinnerung`,
                          `zeit_erinnerung`,
                          `stages`,
                          `adresse`
                          )
                VALUES(:bezeichnung, :datum_angelegt, :zeit_angelegt, :datum_erinnerung, :zeit_erinnerung, :stages, :adr)',
                $xtDeal
            );

            $latestId = $this->db->lastInsertId();

            if ($data = $this->gateway->getMappingStageByResubmissionStageId($xtDeal['stages'])) {
                $viewId = $data['wiedervorlage_view_id'];
                // update
                $eventMsg = sprintf(
                    'Neues Deal(<a href="/index.php?module=wiedervorlage&action=list&view=%d">%s</a>) vom Hubspot importiert.',
                    $viewId,
                    $xtDeal['bezeichnung']
                );

                $this->eventService->add($eventMsg);
            }

            return $latestId;
        }

        return 0;
    }

    /**
     * @param int $dealId
     * @param int $wvId
     * @param int $addressId
     *
     * @throws Exception
     */
    private function updateXTDeal($dealId, $wvId, $addressId = 0)
    {
        if ($xtDeal = $this->configuration->formatDealByResponse($this->dealService->getDealById($dealId))) {
            $xtDeal['adr'] = $addressId;
            $affected = $this->db->fetchAffected(
                'UPDATE `wiedervorlage`
                SET `bezeichnung` = :bezeichnung,
                    `datum_angelegt` = :datum_angelegt,
                    `zeit_angelegt` = :zeit_angelegt,
                    `datum_erinnerung` = :datum_erinnerung,
                    `zeit_erinnerung` = :zeit_erinnerung,
                    `stages` = :stages,
                    `adresse` = :adr
                WHERE `id` =' . $wvId,
                $xtDeal
            );

            if ($affected > 0) {
                $data = $this->gateway->getMappingStageByResubmissionStageId($xtDeal['stages']);
                if (empty($data)) {
                    return;
                }
                $viewId = $data['wiedervorlage_view_id'];
                // update
                $eventMsg = sprintf(
                    'Ge&auml;ndertes Deal(<a href="/index.php?module=wiedervorlage&action=list&view=%d">%s</a>) vom Hubspot importiert.',
                    $viewId,
                    $xtDeal['bezeichnung']
                );
                $this->eventService->add($eventMsg);
            }
        }
    }
}
