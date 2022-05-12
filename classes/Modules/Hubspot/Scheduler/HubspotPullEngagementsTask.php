<?php

declare(strict_types=1);

namespace Xentral\Modules\Hubspot\Scheduler;

use ArrayObject;
use Xentral\Components\Database\Database;
use Xentral\Modules\Hubspot\Exception\HubspotConfigurationServiceException;
use Xentral\Modules\Hubspot\Exception\HubspotException;
use Xentral\Modules\Hubspot\Exception\MetaException;
use Xentral\Modules\Hubspot\HubspotConfigurationService;
use Xentral\Modules\Hubspot\HubspotContactGateway;
use Xentral\Modules\Hubspot\HubspotEngagementService;
use Xentral\Modules\Hubspot\HubspotEventService;
use Xentral\Modules\Hubspot\HubspotMetaService;
use Xentral\Modules\SubscriptionCycle\Scheduler\TaskMutexServiceInterface;

final class HubspotPullEngagementsTask implements HubspotSchedulerTaskInterface
{

    /** @var int[] $itemsCount */
    private $itemsCount = ['count' => 100];

    /** @var Database $db */
    private $db;

    /** @var HubspotEngagementService $engagementsService */
    private $engagementsService;

    /** @var HubspotMetaService $meta */
    private $meta;

    /** @var HubspotConfigurationService $configuration */
    private $configuration;

    /** @var HubspotEventService $event */
    private $event;

    /** @var HubspotContactGateway $contactGateway */
    private $contactGateway;

    /** @var TaskMutexServiceInterface $taskMutexService */
    private $taskMutexService;

    /**
     * @param Database                    $db
     * @param HubspotEngagementService    $engagementService
     * @param HubspotMetaService          $metaService
     * @param HubspotConfigurationService $configuration
     * @param HubspotEventService         $eventService
     * @param HubspotContactGateway       $contactGateway
     * @param TaskMutexServiceInterface   $taskMutexService
     */
    public function __construct(
        Database $db,
        HubspotEngagementService $engagementService,
        HubspotMetaService $metaService,
        HubspotConfigurationService $configuration,
        HubspotEventService $eventService,
        HubspotContactGateway $contactGateway,
        TaskMutexServiceInterface $taskMutexService
    ) {
        $this->db = $db;
        $this->engagementsService = $engagementService;
        $this->configuration = $configuration;
        $this->meta = $metaService;
        $this->event = $eventService;
        $this->contactGateway = $contactGateway;
        $this->taskMutexService = $taskMutexService;
    }

    /**
     * @throws HubspotException
     *
     * @return void
     */
    public function execute(): void
    {
        if ($this->taskMutexService->isTaskInstanceRunning('hubspot_pull_engagements')) {
            return;
        }
        $this->taskMutexService->setMutex('hubspot_pull_engagements');

        try {
            $settings = $this->configuration->getSettings();
        } catch (HubspotConfigurationServiceException $e) {
            return;
        }
        if (empty($settings['hs_sync_engagements'])) {
            return;
        }
        $this->recursiveExecute();
    }

    /**
     * @param array $option
     *
     * @throws HubspotException
     *
     * @return void
     */
    private function recursiveExecute(array $option = []): void
    {
        $remainingData = $this->pull($option);

        if (!empty($remainingData['has_more'])) {
            unset($remainingData['has_more']);
            $this->recursiveExecute($remainingData);
        }
    }

    /**
     * @param array $options
     *
     * @throws HubspotException
     *
     * @return array|null
     */
    private function pull(array $options = []): ?array
    {
        $options = array_merge($options, $this->itemsCount);

        $metaInfo = $this->meta->setName('recent_engagements')->get();
        $requestData = [];
        if (array_key_exists('since', $metaInfo)) {
            $requestData['since'] = $metaInfo['since'];
        }
        if (array_key_exists('offset', $metaInfo)) {
            $requestData['offset'] = $metaInfo['offset'];
        }
        $options = array_merge($options, $requestData);
        $response = $this->engagementsService->getRecentEngagements($options);

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $data = $response->getJson();

        $offSet = array_key_exists('offset', $data) ? $data['offset'] : 0;
        $hasMore = array_key_exists('hasMore', $data) ? $data['hasMore'] : false;
        $total = array_key_exists('total', $data) ? $data['total'] : 0;
        $since = array_key_exists('since', $data) ? $data['since'] : time() * 1000;

        if ($total === 0 || !array_key_exists('results', $data)) {
            return null;
        }

        $engagements = $data['results'];

        foreach ($engagements as $engagement) {
            $engagementData = $engagement['engagement'];
            // ONLY TYPE NOTE IS CURRENTLY ALLOWED
            if ($engagementData['type'] !== 'NOTE') {
                continue;
            }
            $engagementId = $engagementData['id'];
            $engagementAssociations = $engagement['associations'];
            $metadata = $engagement['metadata'];
            $body = $metadata['body'];
            $companyIds = null;
            $contactIds = null;
            if ($this->engagementExists($engagementId)) {
                continue;
            }
            foreach ($engagementAssociations as $intended => $values) {
                if (!is_string($intended)) {
                    continue;
                }

                if ($intended === 'companyIds') {
                    $companyIds = $values;
                }

                if ($intended === 'contactIds') {
                    $contactIds = $values;
                }
            }

            if (empty($companyIds) && empty($contactIds)) {
                continue;
            }

            $intendedIds = array_merge($companyIds, $contactIds);
            foreach ($intendedIds as $intendedId) {
                $noteId = $this->addNote($intendedId, $body);
                if (empty($noteId)) {
                    continue;
                }

                $mapping = $this->contactGateway->getMappingByHubspotId($intendedId, null);
                if (empty($mapping)) {
                    continue;
                }
                $addressId = $mapping['address_id'];
                $this->db->perform(
                    'INSERT INTO `hubspot_contacts` (`hs_contact_id`, `created_at`, `address_id`, `type`)
                        VALUES (:id, NOW(), :aid, :type)',
                    ['id' => $engagementId, 'aid' => $noteId, 'type' => 'note']
                );
                $eventItem = 'Adresse';

                $eventMsg = sprintf(
                    'Neue Notiz vom Hubspot hinzugef&uuml;gt ins Xentral f&uuml;r (<a href="/index.php?module=adresse&action=edit&id=%d">%s</a>) importiert.',
                    $addressId,
                    $eventItem
                );
                $this->event->add($eventMsg);
            }
        }

        $remainingData = ['offset' => $offSet, 'since' => $since];

        try {
            $this->meta->setName('recent_engagements')->save($remainingData);
        } catch (MetaException $e) {
            $this->event->add($e->getMessage());
        }

        $remainingData['has_more'] = $hasMore;

        return $remainingData;
    }

    public function cleanup()
    {
        $this->taskMutexService->setMutex('hubspot_pull_engagements', false);
    }

    public function beforeScheduleAction(ArrayObject $args)
    {
    }

    public function afterScheduleAction(ArrayObject $args)
    {
    }

    /**
     * @param int    $hubspotContactId
     * @param string $body
     *
     * @return int
     */
    private function addNote(int $hubspotContactId, string $body): int
    {

        $mapping = $this->contactGateway->getMappingByHubspotId($hubspotContactId, null);
        if (empty($mapping)) {
            return 0;
        }

        $sql = 'INSERT INTO `dokumente` (
                         `adresse_to`,
                         `adresse_from`,
                         `typ`,
                         `betreff`,
                         `content`,
                         `datum`,
                         `uhrzeit`,
                         `created`,
                         `bearbeiter`)
                VALUES (:address, 1, :type, :object, :body, :date, :time, NOW(), :editor)';
        $this->db->perform(
            $sql,
            [
                'address' => $mapping['address_id'],
                'type'    => 'notiz',
                'object'  => 'Hubspot note',
                'body'    => $body,
                'date'    => date('Y-m-d'),
                'time'    => date('H:i:s'),
                'editor'  => 'HubspotModule',
            ]
        );

        return $this->db->lastInsertId();
    }

    /**
     * @param int $engagementId
     *
     * @return bool
     */
    private function engagementExists(int $engagementId) : bool
    {
        return $this->contactGateway->hubspotContactExists($engagementId, ['note']);
    }
}
