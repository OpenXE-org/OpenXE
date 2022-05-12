<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Scheduler;

use Xentral\Components\Database\Database;
use ArrayObject;
use Xentral\Components\Database\Exception\EscapingException;
use Xentral\Modules\Pipedrive\Exception\PipedriveConfigurationException;
use Xentral\Modules\Pipedrive\Exception\PipedriveEventException;
use Xentral\Modules\Pipedrive\Exception\PipedriveMetaException;
use Xentral\Modules\Pipedrive\Exception\PipedrivePersonServiceException;
use Xentral\Modules\Pipedrive\Gateway\PipedriveContactGateway;
use Xentral\Modules\Pipedrive\Service\PipedriveConfigurationService;
use Xentral\Modules\Pipedrive\Service\PipedriveEventService;
use Xentral\Modules\Pipedrive\Service\PipedriveMetaReaderService;
use Xentral\Modules\Pipedrive\Service\PipedriveMetaWriterService;
use Xentral\Modules\Pipedrive\Service\PipedrivePersonService;

final class PipedrivePullPersonsTask implements PipedriveSchedulerTaskInterface
{

    /** @var PipedrivePersonService $contactService */
    private $contactService;

    /** @var Database $db */
    private $db;

    /** @var PipedriveMetaWriterService $metaWriterService */
    private $metaWriterService;

    /** @var PipedriveContactGateway $gateway */
    private $gateway;

    /** @var PipedriveConfigurationService $configuration */
    private $configuration;

    /** @var PipedriveEventService $eventService */
    private $eventService;

    /** @var PipedriveMetaReaderService $metaReaderService */
    private $metaReaderService;

    /**
     * @param PipedrivePersonService        $contactService
     * @param Database                      $db
     * @param PipedriveMetaWriterService    $metaService
     * @param PipedriveContactGateway       $gateway
     * @param PipedriveConfigurationService $configuration
     * @param PipedriveEventService         $eventService
     * @param PipedriveMetaReaderService    $metaReaderService
     */
    public function __construct(
        PipedrivePersonService $contactService,
        Database $db,
        PipedriveMetaWriterService $metaService,
        PipedriveContactGateway $gateway,
        PipedriveConfigurationService $configuration,
        PipedriveEventService $eventService,
        PipedriveMetaReaderService $metaReaderService
    ) {
        $this->db = $db;
        $this->contactService = $contactService;
        $this->metaWriterService = $metaService;
        $this->gateway = $gateway;
        $this->configuration = $configuration;
        $this->eventService = $eventService;
        $this->metaReaderService = $metaReaderService;
    }

    /**
     * @param array       $option
     * @param string|null $type
     * @param bool        $recursiveMode
     *
     * @throws EscapingException
     * @throws PipedriveConfigurationException
     * @throws PipedriveEventException
     * @throws PipedriveMetaException
     * @throws PipedrivePersonServiceException
     *
     * @return void
     */
    public function execute(array $option = [], ?string $type = null, bool $recursiveMode = false): void
    {
        $settings = $this->configuration->getSettings();
        if ($settings['pd_sync_addresses'] !== true) {
            return;
        }

        $type = $type ?? 'pipedrive_recently_updated';
        if (empty($recursiveMode) && $this->db->fetchValue(
                'SELECT COUNT(id) FROM `pipedrive_contacts` WHERE `hidden` = 0'
            ) > 0) {
            $type = $type === 'all' ? 'pipedrive_recently_updated' : $type;
        }

        $ret = $this->pull($type, $option);
        if (!empty($ret['has_more'])) {
            $this->execute($option, $type, true);
        }
    }

    /**
     * @return void
     */
    public function cleanup(): void
    {
    }

    /**
     * @param string $type
     * @param array  $options
     *
     * @throws EscapingException
     * @throws PipedriveConfigurationException
     * @throws PipedriveEventException
     * @throws PipedriveMetaException
     * @throws PipedrivePersonServiceException
     *
     * @return array
     */
    protected function pull(string $type = 'pipedrive_recently_updated', array $options = []): array
    {
        $response = $this->contactService->pullPersons($type, $options);

        if ($response->getStatusCode() !== 200) {
            return [];
        }
        $persons = $response->getData();
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
            $this->metaWriterService->save($metaFile, ['timeOffset' => date('Y-m-d H:i:s')]);
        } elseif (array_key_exists('has_more', $options) && $options['has_more'] === true) {
            $timeOffset = $options['previous_timeOffset'] ?? '1970-01-01 23:59:59';
            $startOffset += 100;
        } else {
            $timeOffset = $metaOption['timeOffset'];
        }

        if (is_array($persons) && count($persons) > 0) {
            foreach ($persons as $contact) {
                $contactData = $contact['data'];
                $contactId = $contact['id'];
                if ($this->gateway->getMappingByPipedriveId($contactId)) {
                    // UPDATE
                    $this->updateXTContact($contactData);
                } else {
                    $this->addXTContact($contactData);
                }
            }
        }

        if (!empty($metaOption) && !array_key_exists('previous_timeOffset', $metaOption)) {
            $this->metaWriterService->save($metaFile, ['timeOffset' => date('Y-m-d H:i:s')]);
        }

        return [
            'has_more'            => $hasMore,
            'previous_timeOffset' => $timeOffset,
            'startOffset'         => $startOffset,
        ];
    }

    /**
     * @param array $contact
     *
     * @throws PipedriveConfigurationException
     * @throws PipedriveMetaException
     * @throws EscapingException
     * @throws PipedriveEventException
     *
     * @return void
     */
    private function addXTContact(array $contact): void
    {
        $internalContact = $this->configuration->formatAddressByResponse($contact);
        if (!$internalContact) {
            return;
        }

        $paramValues = array_map(
            function ($value) {
                if (empty($value)) {
                    return "''";
                }
                return is_string($value) ? $this->db->escapeString($value) : $value;
            },
            array_values($internalContact)
        );
        $placeHolders = implode(',', array_fill(0, count($paramValues), '%s'));

        $insertSql = sprintf(
            'INSERT INTO `adresse` (%s) VALUES(%s)',
            implode(',', array_keys($internalContact)),
            vsprintf($placeHolders, $paramValues)
        );
        $this->db->perform($insertSql);
        if ($addressId = $this->db->lastInsertId()) {
            $this->db->perform(
                'INSERT INTO `pipedrive_contacts` 
                (`pd_contact_id`, `created_at`, `address_id`) VALUES (:id, NOW(), :aid)',
                ['id' => (int)$contact['id'], 'aid' => $addressId]
            );

            $personName = !empty($internalContact['email'])? $internalContact['email'] : $internalContact['name'];
            $eventMsg = sprintf(
                'Neuen Kontakt (<a href="/index.php?module=adresse&action=edit&id=%d">%s</a>) hinzugef&uuml;gt.',
                $addressId,
                $personName
            );
            $this->eventService->add($eventMsg);
            // ADD to group
            $this->configuration->addContactToGroup($addressId);
        }
    }

    /**
     * @param array $contact
     *
     * @throws PipedriveConfigurationException
     * @throws PipedriveEventException
     *
     * @return void
     */
    private function updateXTContact(array $contact): void
    {
        $internalContact = $this->configuration->formatAddressByResponse($contact);
        if (!$internalContact) {
            return;
        }
        $excludeVars = ['lead', 'typ', 'sprache', 'waehrung', 'kundenfreigabe'];
        foreach ($excludeVars as $excludeVar) {
            unset($internalContact[$excludeVar]);
        }

        if ($hPdContact = $this->gateway->getMappingByPipedriveId($contact['id'])) {
            $asPlaceHolders = array_map(
                static function ($val) {
                    return vsprintf('%s=:%s', [$val, $val]);
                },
                array_keys($internalContact)
            );
            $placeHolders = implode(',', $asPlaceHolders);

            $affected = $this->db->fetchAffected(
                'UPDATE `adresse` SET ' . $placeHolders . ' WHERE `id` = ' . $hPdContact['address_id'],
                $internalContact
            );
            if ($affected > 0) {
                $personName = !empty($internalContact['email'])? $internalContact['email'] : $internalContact['name'];
                $eventMsg = sprintf(
                    'Kontakt (<a href="/index.php?module=adresse&action=edit&id=%d">%s</a>) ge&auml;ndert.',
                    $hPdContact['address_id'],
                    $personName
                );
                $this->eventService->add($eventMsg);
            }
        }
    }

    /**
     * @param ArrayObject $data
     *
     * @throws PipedriveConfigurationException
     *
     * @return void
     */
    public function beforeScheduleAction(ArrayObject $data): void
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
     *
     * @return void
     */
    public function afterScheduleAction(ArrayObject $data): void
    {
    }
}
