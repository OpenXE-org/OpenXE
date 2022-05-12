<?php

namespace Xentral\Modules\Hubspot\Scheduler;

use JsonException;
use Xentral\Components\Database\Database;
use Xentral\Components\Database\Exception\EscapingException;
use Xentral\Modules\Country\Gateway\CountryGateway;
use Xentral\Modules\Hubspot\Exception\HubspotConfigurationServiceException;
use Xentral\Modules\Hubspot\Exception\HubspotException;
use Xentral\Modules\Hubspot\Exception\MetaException;
use Xentral\Modules\Hubspot\HubspotConfigurationService;
use Xentral\Modules\Hubspot\HubspotContactService;
use Xentral\Modules\Hubspot\HubspotEventService;
use Xentral\Modules\Hubspot\HubspotContactGateway;
use Xentral\Modules\Hubspot\HubspotMetaService;

use ArrayObject;
use Xentral\Modules\SubscriptionCycle\Scheduler\TaskMutexServiceInterface;

final class HubspotPullContactsTask implements HubspotSchedulerTaskInterface
{

    /** @var HubspotContactService $contactService */
    private $contactService;

    /** @var Database $db */
    private $db;

    /** @var HubspotMetaService $meta */
    private $meta;

    /** @var HubspotContactGateway $gateway */
    private $gateway;

    /** @var HubspotConfigurationService $configuration */
    private $configuration;

    /** @var HubspotEventService $eventService */
    private $eventService;

    /** @var CountryGateway $countryGateway */
    private $countryGateway;

    /** @var TaskMutexServiceInterface $taskMutexService */
    private $taskMutexService;

    /** @var bool $mutexOn */
    private $mutexOn = false;

    /**
     * @param HubspotContactService       $contactService
     * @param Database                    $db
     * @param HubspotMetaService          $metaService
     * @param HubspotContactGateway       $gateway
     * @param HubspotConfigurationService $configuration
     * @param HubspotEventService         $eventService
     * @param CountryGateway              $countryGateway
     * @param TaskMutexServiceInterface   $taskMutexService
     */
    public function __construct(
        HubspotContactService $contactService,
        Database $db,
        HubspotMetaService $metaService,
        HubspotContactGateway $gateway,
        HubspotConfigurationService $configuration,
        HubspotEventService $eventService,
        CountryGateway $countryGateway,
        TaskMutexServiceInterface $taskMutexService
    ) {
        $this->db = $db;
        $this->contactService = $contactService;
        $this->meta = $metaService;
        $this->gateway = $gateway;
        $this->configuration = $configuration;
        $this->eventService = $eventService;
        $this->countryGateway = $countryGateway;
        $this->taskMutexService = $taskMutexService;
    }

    /**
     * @param array $option
     * @param null  $type
     * @param false $recursiveMode
     *
     * @throws EscapingException
     * @throws HubspotConfigurationServiceException
     * @throws HubspotException
     * @throws JsonException
     * @throws MetaException
     *
     * @return void
     */
    public function execute($option = [], $type = null, $recursiveMode = false): void
    {
        if ($recursiveMode === false && $this->mutexOn === false) {
            if ($this->taskMutexService->isTaskInstanceRunning('hubspot_pull_contacts')) {
                return;
            }
            $this->taskMutexService->setMutex('hubspot_pull_contacts');
            $this->mutexOn = true;
        }

        $settings = $this->configuration->getSettings();
        if ($settings['hs_sync_addresses'] !== true) {
            return;
        }
        $contactType = $type ?? 'all';

        if (empty($recursiveMode) && $this->hasHsContacts() === true) {
            $contactType = $contactType === 'all' ? 'recently_updated' : $contactType;
        }

        if ($contactType === 'company') {
            $contactType = 'companies';
        }

        $ret = $this->pull($contactType, $option);
        if (!empty($ret['has_more'])) {
            $option = ['vidOffset' => $ret['vidOffset'], 'timeOffset' => $ret['timeOffset']];
            usleep(5000000);
            $this->execute($option, $contactType, true);
        }
    }

    /**
     * @param array $option
     * @param null  $type
     * @param false $recursiveMode
     *
     * @throws EscapingException
     * @throws HubspotConfigurationServiceException
     * @throws HubspotException
     * @throws JsonException
     * @throws MetaException
     *
     * @return void
     */
    protected function executeForCompany($option = [], $type = null, $recursiveMode = false): void
    {
        if ($recursiveMode === false && $this->mutexOn === false) {
            if ($this->taskMutexService->isTaskInstanceRunning('hubspot_pull_contacts')) {
                return;
            }
            $this->taskMutexService->setMutex('hubspot_pull_contacts');
            $this->mutexOn = true;
        }
        $settings = $this->configuration->getSettings();
        if ($settings['hs_sync_addresses'] !== true) {
            return;
        }
        $contactType = $type ?? 'company';


        if (empty($recursiveMode) && $this->hasHsContacts() === true) {
            $contactType = $contactType === 'company' ? 'recent_companies' : $contactType;
        }

        $ret = $this->pull($contactType, $option);

        if (!empty($ret['has_more'])) {
            $option = ['vidOffset' => $ret['vidOffset'], 'timeOffset' => $ret['timeOffset']];
            usleep(5000000);
            $this->executeForCompany($option, $contactType, true);
        }
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function hasHsContacts(string $type = 'address'): bool
    {
        return $this->db->fetchValue(
                'SELECT COUNT(`id`) FROM `hubspot_contacts` WHERE `hidden` = 0 AND `type` = :type',
                ['type' => $type]
            ) > 0;
    }

    /**
     * @param string $email
     * @param int    $addressId
     *
     * @return bool
     */
    private function contactPersonExists(string $email, int $addressId): bool
    {
        return $this->db->fetchValue(
                'SELECT `id` FROM `ansprechpartner` WHERE `email` = :email AND `adresse` = :addressId',
                ['email' => $email, 'addressId' => $addressId]
            ) > 0;
    }

    /**
     * @param string $name
     *
     * @return int
     */
    private function getAddressIdByCompanyName(string $name): int
    {
        return $this->db->fetchValue(
            "SELECT ad.id FROM `adresse` AS `ad` JOIN `hubspot_contacts` AS `hs`
                ON(ad.id = hs.address_id)
                WHERE ad.firma = 1 AND
                       ad.typ = 'firma' AND
                      hs.type = 'company' AND
                      ad.name = :name LIMIT 1",
            ['name' => $name]
        );
    }

    /**
     * @return void
     */
    public function cleanup(): void
    {
        $this->taskMutexService->setMutex('hubspot_pull_contacts', false);
    }

    /**
     * @param array $companies
     *
     * @throws HubspotException
     * @throws EscapingException
     *
     * @return void
     */
    private function importCompany(array $companies): void
    {
        $settings = $this->configuration->getSettings();
        foreach ($companies as $company) {
            $companyId = $company['companyId'];

            if ($this->gateway->getMappingByHubspotId($companyId, 'company')) {
                $this->updateXTContact($companyId, 'company');
                continue;
            }
            $properties = $company['properties'];
            $companyData = array_combine(array_keys($properties), array_column($properties, 'value'));
            $companyNameTag = $properties['name'];
            $contactSourceIds = array_combine(array_keys($properties), array_column($properties, 'sourceId'));
            $hsLeadStatus = array_key_exists('hs_lead_status', $companyData) ? $companyData['hs_lead_status'] : '';
            $createdAt = $companyNameTag['timestamp'];
            $sourceEmail = $companyNameTag['sourceId'];
            if (empty($sourceEmail)) {
                $sourceEmail = $contactSourceIds['name'];
            }

            $country = empty($companyData['country']) ? 'DE' : $companyData['country'];
            if ($country !== 'DE') {
                $countryDb = $this->countryGateway->findByName($country);
                if (!empty($countryDb)) {
                    $country = $countryDb['iso2_code'];
                }
            }
            $address = [
                'typ'            => 'firma',
                'sprache'        => 'deutsch',
                'name'           => $companyData['name'],
                'land'           => $country,
                'email'          => $sourceEmail,
                'kundenfreigabe' => 1,
                'firma'          => 1,
                'waehrung'       => 'EUR',
                'internetseite'  => empty($companyData['website']) ? '' : $companyData['website'],
                'ort'            => empty($companyData['city']) ? '' : $companyData['city'],
                'plz'            => empty($companyData['zip']) ? '' : $companyData['zip'],
                'strasse'        => empty($companyData['address']) ? '' : $companyData['address'],
            ];

            try {
                $leadFields = $this->configuration->matchSelectedAddressFreeField();
                $lrField = $leadFields['hubspot_lr_field'];
                $lsField = $leadFields['hubspot_ls_field'];
                $address[$lsField] = $hsLeadStatus;
                $address[$lrField] = empty($companyData['lifecyclestage']) ? '' : $companyData['lifecyclestage'];
            } catch (HubspotException $exception) {
                $this->eventService->add($exception->getMessage());
            }

            $numberOfEmployeesField = $this->configuration->tryGetConfiguration('hubspot_numberofemployees_field');
            if (!empty($numberOfEmployeesField)) {
                $fieldName = str_replace('adresse', '', $numberOfEmployeesField);
                $numberOfEmployees = empty($companyData['numberofemployees']) ? 0 : $companyData['numberofemployees'];
                $address[$fieldName] = $numberOfEmployees;
            }

            $defaultCustomFields = array_key_exists('hubspot_address_free_fields', $settings) ?
                $settings['hubspot_address_free_fields'] : [];
            if (!empty($defaultCustomFields)) {
                foreach ($defaultCustomFields as $defaultCustomField => $systemField) {
                    $fieldName = str_replace('adresse', '', $systemField);
                    $fieldValue = empty($companyData[$defaultCustomField]) ? '' : $companyData[$defaultCustomField];
                    $address[$fieldName] = $fieldValue;
                }
            }

            if (!empty($createdAt) &&
                array_key_exists('hs_sync_addresses_from', $settings) &&
                !empty($settings['hs_sync_addresses_from'])
            ) {
                $addedTime = $createdAt / 1000;
                if ($addedTime < $settings['hs_sync_addresses_from']) {
                    continue;
                }
            }
            // Status
            if (array_key_exists('hs_sync_address_status', $settings) &&
                !empty($settings['hs_sync_address_status']) &&
                $hsLeadStatus !== $settings['hs_sync_address_status']
            ) {
                continue;
            }

            $hubspotOwnerId = (int)array_key_exists(
                'hubspot_owner_id',
                $companyData
            ) ? $companyData['hubspot_owner_id'] : 0;
            if ($hubspotOwnerId !== 0) {
                $staffId = $this->manageSaleStaffPerson($companyId, $hubspotOwnerId);
                if ($staffId !== 0) {
                    $address['vertrieb'] = $staffId;
                }
            }

            $this->addXTContact($companyId, 'company', $address);
        }
    }

    /**
     * @param string $type
     * @param array  $options
     *
     * @throws EscapingException
     * @throws HubspotConfigurationServiceException
     * @throws HubspotException
     * @throws MetaException
     * @throws JsonException
     *
     * @return array
     */
    protected function pull($type = 'recently_updated', $options = []): array
    {
        $response = in_array($type, ['company', 'recent_companies']) ?
            $this->contactService->pullCompanies($type, $options) :
            $this->contactService->pullContacts($type, $options);

        if ($response->getStatusCode() !== 200) {
            return [];
        }

        $data = $response->getJson();
        $offSet = array_key_exists('vid-offset', $data) ? $data['vid-offset'] : 0;
        $singleOffset = array_key_exists('offset', $data) ? $data['offset'] : -1;
        $hasMore = array_key_exists('has-more', $data) ? $data['has-more'] : false;
        $timeOffset = array_key_exists('time-offset', $data) ? $data['time-offset'] : 0;
        if (array_key_exists('companies', $data) || $type === 'recent_companies') {
            $companyResponse = $type !== 'recent_companies' ? $data['companies'] : $data['results'];
            $this->importCompany($companyResponse);
        }

        if (array_key_exists('contacts', $data)) {
            $settings = $this->configuration->getSettings();
            $contacts = $data['contacts'];
            if (count($contacts) > 0) {
                foreach ($contacts as $contact) {
                    $contactId = $contact['vid'];
                    $properties = $contact['properties'];
                    $createdAt = $contact['addedAt'];
                    $contactData = array_combine(array_keys($properties), array_column($properties, 'value'));
                    $hsLeadStatus = array_key_exists('hs_lead_status', $contactData) ?
                        $contactData['hs_lead_status'] : '';
                    $email = '';

                    $identityProfile = $contact['identity-profiles'];
                    if (!empty($identityProfile)) {
                        $identities = array_column($identityProfile, 'identities');
                        foreach ($identities as $identity) {
                            foreach ($identity as $item) {
                                if ($item['type'] === 'EMAIL') {
                                    $email = $item['value'];
                                    break;
                                }
                            }
                        }
                    }

                    $contactCompany = array_key_exists('company', $contactData) ? $contactData['company'] : '';

                    if (!empty($contactCompany) && ($addressId = $this->getAddressIdByCompanyName(
                            $contactCompany
                        ))) {
                        $contactPerson = [
                            'type'    => 'herr',
                            'name'    => sprintf(
                                '%s %s',
                                empty($contactData['firstname']) ? 'Hubspot - ' : $contactData['firstname'],
                                empty($contactData['lastname']) ? '' : $contactData['lastname']
                            ),
                            'adresse' => $addressId,
                            'email'   => $email,
                            'land'    => 'DE',
                            'phone'   => $contactData['phone'],
                        ];

                        if ($this->contactPersonExists($email, $addressId) === true) {
                            continue;
                        }
                        $this->addContactPerson($contactPerson, $contactId);
                        continue;
                    }

                    if ($this->gateway->getMappingByHubspotId($contactId)) {
                        // UPDATE
                        if ($type === 'recently_updated') {
                            $this->updateXTContact($contactId);
                        }
                        continue;
                    }

                    if (!empty($createdAt) &&
                        array_key_exists('hs_sync_addresses_from', $settings) &&
                        !empty($settings['hs_sync_addresses_from'])
                    ) {
                        $addedTime = $createdAt / 1000;
                        if ($addedTime < $settings['hs_sync_addresses_from']) {
                            continue;
                        }
                    }
                    // Status
                    if (array_key_exists('hs_sync_address_status', $settings) &&
                        !empty($settings['hs_sync_address_status']) &&
                        $hsLeadStatus !== $settings['hs_sync_address_status']
                    ) {
                        continue;
                    }

                    $this->addXTContact($contactId);
                }
            }
        }

        if ($timeOffset === 0) {
            $timeOffset = time() * 1000;
        }

        $remainingData = ['vidOffset' => $offSet, 'timeOffset' => $timeOffset];
        if ($singleOffset !== -1) {
            $remainingData['offset'] = $singleOffset;
        }
        $this->meta->setName($type)->save($remainingData);
        $remainingData['has_more'] = $hasMore;

        return $remainingData;
    }

    /**
     * @param int    $contactId
     * @param string $type
     * @param array  $contact
     *
     * @throws HubspotException
     * @throws EscapingException
     *
     * @return void
     */
    private function addXTContact(int $contactId = 0, string $type = 'address', array $contact = []): void
    {
        $addressContact = $contact;
        if (empty($contact)) {
            $addressContact = $this->configuration->formatAddressByResponse(
                $this->contactService->getContactById($contactId)
            );
        }
        if (empty($addressContact)) {
            return;
        }

        $hubspotOwnerId = (int)$addressContact['hubspot_owner_id'];
        unset($addressContact['hubspot_owner_id']);
        if ($hubspotOwnerId !== 0) {
            $staffId = $this->manageSaleStaffPerson($contactId, $hubspotOwnerId);
            if ($staffId !== 0) {
                $addressContact['vertrieb'] = $staffId;
            }
        }

        $paramValues = array_map(
            function ($value) {
                if (empty($value)) {
                    return '\'\'';
                }

                return is_string($value) ? $this->db->escapeString($value) : $value;
            },
            array_values($addressContact)
        );
        $placeHolders = implode(',', array_fill(0, count($paramValues), '%s'));

        $sql = 'INSERT INTO adresse(' . implode(',', array_keys($addressContact)) . ')
                            VALUES(' . vsprintf($placeHolders, $paramValues) . ')';
        $this->db->perform($sql);
        if ($addressId = $this->db->lastInsertId()) {
            $this->db->perform(
                'INSERT INTO `hubspot_contacts` (`hs_contact_id`, `created_at`, `address_id`, `type`)
                        VALUES (:id, NOW(), :aid, :type)',
                ['id' => $contactId, 'aid' => $addressId, 'type' => $type]
            );
            $eventItem = !empty($addressContact['email']) ? $addressContact['email'] : $addressContact['name'];

            $eventMsg = sprintf(
                'Neuen Kontakt (<a href="/index.php?module=adresse&action=edit&id=%d">%s</a>) vom Hubspot hinzugef&uuml;gt ins Xentral importiert.',
                $addressId,
                $eventItem
            );
            $this->eventService->add($eventMsg);
            // ADD to group
            $this->configuration->addContactToGroup($addressId);
            // get companies contacts
            if ($type === 'company') {
                $this->importCompanyContactPersons($contactId, $addressId);
            }
        }
    }

    /**
     * @param int         $hubspotContactId
     * @param string|null $type
     *
     * @throws HubspotException
     *
     * @return void
     */
    private function updateXTContact(int $hubspotContactId = 0, ?string $type = 'address'): void
    {
        $remoteResponse = $type === 'company' ? $this->contactService->getCompanyById($hubspotContactId) :
            $this->contactService->getContactById($hubspotContactId);
        if ($remoteResponse->getStatusCode() !== 200) {
            return;
        }

        try {
            $xtContact = $type === 'company' ? $this->configuration->formatCompanyByResponse($remoteResponse) :
                $this->configuration->formatAddressByResponse($remoteResponse);
        } catch (HubspotException $exception) {
            $this->eventService->add($exception->getMessage());

            return;
        }

        $excludeVars = ['lead', 'typ', 'sprache', 'waehrung', 'kundenfreigabe'];
        foreach ($excludeVars as $excludeVar) {
            if (array_key_exists($excludeVar, $xtContact)) {
                unset($xtContact[$excludeVar]);
            }
        }

        $hubspotOwnerId = (int)$xtContact['hubspot_owner_id'];
        unset($xtContact['hubspot_owner_id']);
        if ($hubspotOwnerId !== 0) {
            $staffId = $this->manageSaleStaffPerson($hubspotContactId, $hubspotOwnerId);
            if ($staffId !== 0) {
                $xtContact['vertrieb'] = $staffId;
            }
        }
        $hHSContact = $this->gateway->getMappingByHubspotId($hubspotContactId, $type);
        if (empty($hHSContact)) {
            return;
        }

        $asPlaceHolders = array_map(
            static function ($val) {
                return vsprintf('%s=:%s', [$val, $val]);
            },
            array_keys($xtContact)
        );

        $placeHolders = implode(',', $asPlaceHolders);

        $affected = $this->db->fetchAffected(
            'UPDATE adresse SET ' . $placeHolders . ' WHERE id=' . $hHSContact['address_id'],
            $xtContact
        );
        $eventItem = !empty($xtContact['email']) ? $xtContact['email'] : $xtContact['name'];
        if ($affected > 0) {
            $eventMsg = sprintf(
                'Kontakt (<a href="/index.php?module=adresse&action=edit&id=%d">%s</a>) vom Hubspot ge&auml;ndert und ins Xentral importiert',
                $hHSContact['address_id'],
                $eventItem
            );
            $this->eventService->add($eventMsg);
        }
        if ($type === 'company') {
            $this->importCompanyContactPersons($hubspotContactId, $hHSContact['address_id']);
        }
    }

    /**
     * @param ArrayObject $args
     *
     * @throws EscapingException
     * @throws HubspotConfigurationServiceException
     * @throws HubspotException
     * @throws JsonException
     * @throws MetaException
     *
     * @return void
     */
    public function beforeScheduleAction(ArrayObject $args): void
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

        $this->executeForCompany();
    }

    /**
     * @param array $contactPerson
     * @param int   $hubspotContactPersonId
     *
     * @throws HubspotConfigurationServiceException
     *
     * @return void
     */
    private function addContactPerson(array $contactPerson, int $hubspotContactPersonId = 0): void
    {
        $this->db->perform(
            'INSERT INTO `ansprechpartner` (
                               `typ`,
                               `name`,
                               `adresse`,
                               `email`,
                               `land`,
                               `logdatei`,
                               `telefon`
                               )
                   VALUES (:type, :name, :adresse, :email, :land, NOW(), :phone)',
            $contactPerson
        );

        $companyContactPersonId = $this->db->lastInsertId();
        if (empty($companyContactPersonId) || empty($hubspotContactPersonId)) {
            return;
        }

        $contactExists = $this->gateway->hubspotContactExists($hubspotContactPersonId, ['address', 'person']);
        if ($contactExists === true) {
            $sql = 'UPDATE `hubspot_contacts` SET `type` = :type, `address_id` = :aid WHERE `hs_contact_id` = :id';
            $this->db->perform(
                $sql,
                [
                    'id'   => $hubspotContactPersonId,
                    'aid'  => $companyContactPersonId,
                    'type' => 'person',
                ]
            );
        } else {
            $this->db->perform(
                'INSERT INTO `hubspot_contacts` (`hs_contact_id`, `created_at`, `address_id`, `type`)
                        VALUES (:id, NOW(), :aid, :type)',
                ['id' => $hubspotContactPersonId, 'aid' => $companyContactPersonId, 'type' => 'person']
            );
        }
        if ($this->isContactPersonInHubspotGroup($companyContactPersonId) === false) {
            $this->addContactPersonToHubspotGroup($companyContactPersonId);
        }
    }

    /**
     * @param int $contactPersonId
     *
     * @throws HubspotConfigurationServiceException
     *
     * @return void
     */
    private function addContactPersonToHubspotGroup(int $contactPersonId): void
    {
        $defaultSettings = $this->configuration->getSettings();

        $contactGrpId = array_key_exists('hs_contact_grp', $defaultSettings) ? $defaultSettings['hs_contact_grp'] : 0;
        if (empty($contactGrpId)) {
            return;
        }
        $sql = 'INSERT INTO `ansprechpartner_gruppen` (`ansprechpartner`, `gruppe`, `aktiv`) VALUES (:id, :group, 1)';
        $this->db->perform($sql, ['id' => $contactPersonId, 'group' => $contactGrpId]);
    }

    /**
     * @param int $contactPersonId
     *
     * @throws HubspotConfigurationServiceException
     *
     * @return bool
     */
    private function isContactPersonInHubspotGroup(int $contactPersonId): bool
    {
        $defaultSettings = $this->configuration->getSettings();

        $contactGrpId = array_key_exists('hs_contact_grp', $defaultSettings) ? $defaultSettings['hs_contact_grp'] : 0;
        if (empty($contactGrpId)) {
            return true;
        }
        $sql = 'SELECT `id` FROM `ansprechpartner_gruppen` WHERE `ansprechpartner` = :id AND `gruppe` = :group';
        $result = $this->db->fetchValue($sql, ['id' => $contactPersonId, 'group' => $contactGrpId]);

        return !empty($result);
    }

    /**
     * @param int $internalContactPersonId
     *
     * @return void
     */
    public function mapPersonToCompany(int $internalContactPersonId): void
    {
        $mappingData = $this->gateway->getHubspotMappingByPersonId($internalContactPersonId);
        if (empty($mappingData)) {
            return;
        }

        if (empty($mappingData['company_id']) || empty($mappingData['contact_id'])) {
            return;
        }
        $this->contactService->addContactToCompany($mappingData['company_id'], $mappingData['contact_id']);
    }

    /**
     * @param int $companyId
     * @param int $addressId
     *
     * @throws HubspotConfigurationServiceException
     *
     * @return void
     */
    private function importCompanyContactPersons(int $companyId, int $addressId): void
    {
        $contactPersonsResponse = $this->contactService->getCompanyContacts($companyId);
        if ($contactPersonsResponse->getStatusCode() !== 200) {
            return;
        }
        $contactPersonsData = $contactPersonsResponse->getJson();
        if (empty($contactPersonsData['contacts'])) {
            return;
        }
        $contactPersons = $contactPersonsData['contacts'];
        foreach ($contactPersons as $contactPerson) {
            $email = '';
            $identities = $contactPerson['identities'];
            $contactPersonVid = array_column($identities, 'vid');
            $rawContact = [];
            $contactPersonId = 0;
            if (!empty($contactPersonVid)) {
                $contactPersonId = $contactPersonVid[0];
                $hubspotContactResponse = $this->contactService->getContactById($contactPersonId);
                if ($hubspotContactResponse->getStatusCode() !== 200) {
                    continue;
                }
                $contactJs = $hubspotContactResponse->getJson();
                $properties = $contactJs['properties'];
                $rawContact = array_combine(
                    array_keys($properties),
                    array_column($properties, 'value')
                );
                $email = array_key_exists('email', $rawContact) ? $rawContact['email'] : '';
            }
            if (empty($rawContact)) {
                $contactPersonIdentity = array_column($identities, 'identity');
                foreach ($contactPersonIdentity as $identity) {
                    foreach ($identity as $item) {
                        if ($item['type'] === 'EMAIL') {
                            $email = $item['value'];
                            break;
                        }
                    }
                }
                $properties = $contactPerson['properties'];
                $rawContact = array_combine(
                    array_column($properties, 'name'),
                    array_column($properties, 'value')
                );
            }
            $contactPersonForDB = [
                'type'    => 'herr',
                'name'    => sprintf(
                    '%s %s',
                    empty($rawContact['firstname']) ? 'Hubspot - ' : $rawContact['firstname'],
                    empty($rawContact['lastname']) ? '' : $rawContact['lastname']
                ),
                'adresse' => $addressId,
                'email'   => $email,
                'land'    => 'DE',
                'phone'   => empty($rawContact['phone']) ? '' : $rawContact['phone'],
            ];

            if ($this->contactPersonExists($email, $addressId) === true) {
                continue;
            }
            $this->addContactPerson($contactPersonForDB, $contactPersonId);
        }
    }

    public function afterScheduleAction(ArrayObject $args): void
    {
        // TODO: Implement afterSchedule() method.
    }

    /**
     * @param int $companyId
     * @param int $hubspotOwnerId
     *
     * @throws HubspotException
     *
     * @return int
     */
    private function manageSaleStaffPerson(int $companyId, int $hubspotOwnerId): int
    {
        if ($companyId === 0 || $hubspotOwnerId === 0) {
            return 0;
        }

        $addressResponse = $this->contactService->getHubspotOwner($hubspotOwnerId);

        $address = $addressResponse->getJson();

        if (empty($address)) {
            return 0;
        }

        $email = $address['email'];
        $sql = 'SELECT id FROM `adresse` WHERE `typ` != :type AND `email` = :email LIMIT 1';
        $addressId = $this->db->fetchValue($sql, ['type' => 'firma', 'email' => $email]);

        $staffExists = $this->gateway->hubspotSaleStaffExists($companyId);
        if ($staffExists === true) {
            $sql = 'UPDATE `hubspot_contacts` SET `address_id` = :aid WHERE `hs_contact_id` = :id AND `data` = :company';
            $this->db->perform(
                $sql,
                [
                    'id'      => $hubspotOwnerId,
                    'aid'     => $addressId,
                    'company' => (string)$companyId,
                ]
            );
        } elseif ($addressId !== 0) {
            $this->db->perform(
                'INSERT INTO `hubspot_contacts` (`hs_contact_id`, `created_at`, `address_id`, `type`, `data`)
                        VALUES (:id, NOW(), :aid, :type, :company)',
                ['id' => $hubspotOwnerId, 'aid' => $addressId, 'type' => 'sale_staff', 'company' => (string)$companyId]
            );
        }

        return $addressId;
    }
}
