<?php

namespace Xentral\Modules\Hubspot;

use JsonException;
use Xentral\Modules\Hubspot\HubspotHttpResponseService as Response;
use Xentral\Modules\Hubspot\Exception\HubspotException;
use Xentral\Modules\Hubspot\Validators\ContactValidator;


final class HubspotContactService
{

    /** @var int[] $itemsCount */
    private $itemsCount = ['count' => 100];

    /** @var string[] $allowedSyncContactOptions */
    private $allowedSyncContactOptions = [
        'recently_created' => 'getRecentlyCreatedContacts',
        'recently_updated' => 'getRecentlyUpdatedContacts',
        'all'              => 'getContacts',
        'companies'        => 'getCompanies',
        'recent_companies' => 'getRecentCompanies',
    ];

    /** @var HubspotClientService $client */
    private $client;

    /** @var HubspotMetaService $meta */
    private $meta;

    /** @var ContactValidator $validator */
    private $validator;

    /** @var HubspotConfigurationService $configurationService */
    private $configurationService;

    /**
     * @param HubspotClientService        $client
     * @param HubspotMetaService          $meta
     * @param ContactValidator            $validator
     * @param HubspotConfigurationService $configurationService
     */
    public function __construct(
        HubspotClientService $client,
        HubspotMetaService $meta,
        ContactValidator $validator,
        HubspotConfigurationService $configurationService
    ) {
        $this->client = $client;
        $this->meta = $meta;
        $this->validator = $validator;
        $this->configurationService = $configurationService;
    }

    /**
     * @param array $options
     *
     * @return Response
     */
    public function getContacts($options = []): Response
    {
        return $this->client->setResource('allContacts')->read($options);
    }

    /**
     * @param array $options
     *
     * @return Response
     */
    public function getRecentlyUpdatedContacts($options = []): Response
    {
        $options += $this->itemsCount;

        return $this->client->setResource('recentlyUpdatedContacts')->read($options);
    }

    /**
     * @param int $contactId
     *
     * @return Response
     */
    public function deleteContact($contactId = 0): Response
    {
        return $this->client->setResource('deleteContact')->delete([], [$contactId]);
    }

    /**
     * @param array $data
     *
     * @throws HubspotException
     *
     * @return HubspotHttpResponseService
     */
    public function createContact($data = []): Response
    {
        if (!$this->validator->isValid($data)) {
            throw new HubSpotException(sprintf('Invalid contact data'));
        }

        $contactData = $this->validator->getData();

        if (array_key_exists('salutation', $data) && $data['salutation'] === 'firma') {
            $contactData['name'] = $data['lastname'];
            $identity = $this->formatCompanyIdentity($contactData);
            return $this->client->setResource('createCompany')->post($identity);
        }

        $identity = $this->formatContactIdentity($contactData);

        return $this->client->setResource('createContact')->post($identity);
    }

    /**
     * @param       $contactId
     * @param array $data
     *
     * @throws HubspotException
     *
     * @return HubspotHttpResponseService
     */
    public function updateContactById($contactId, $data = []): Response
    {
        if (!$this->validator->isValid($data)) {
            throw new HubspotException(sprintf('Invalid contact data'));
        }

        $contactData = $this->validator->getData();
        if (array_key_exists('salutation', $data) && $data['salutation'] === 'firma') {
            $contactData['name'] = $data['lastname'];
            $identity = $this->formatCompanyIdentity($contactData);
            return $this->client->setResource('updateCompany')->put($identity, [$contactId]);
        }

        $identity = $this->formatContactIdentity($contactData);

        return $this->client->setResource('updateContact')->post($identity, [$contactId]);
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    private function formatContactIdentity($data)
    {
        $identity['properties'] = [];
        foreach ($data as $property => $value) {
            $identity['properties'][] = [
                'property' => $property,
                'value'    => $value,
            ];
        }

        return $identity;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function formatCompanyIdentity(array $data) : array
    {
        $whiteList = ['name',
                      'phone',
                      'hs_lead_status',
                      'website',
                      'domain',
                      'lifecyclestage',
                      'zip',
                      'city',
                      'country',
                      'state',
                      'address',
                      'numberofemployees',
                      'description'
        ];

        $settings = $this->configurationService->getSettings();
        $defaultCustomFields = array_key_exists('hubspot_address_free_fields', $settings) ?
            $settings['hubspot_address_free_fields'] : [];
        if (!empty($defaultCustomFields)) {
            foreach ($defaultCustomFields as $defaultCustomField => $systemField) {
                $whiteList[] = "xthubspot_{$defaultCustomField}";
            }
        }

        $identity['properties'] = [];
        foreach ($data as $property => $value) {
            if (!in_array($property, $whiteList, true)) {
                continue;
            }
            if (strpos($property, 'xthubspot_') !== false) {
                $property = str_replace('xthubspot_', '', $property);
            }
            $identity['properties'][] = [
                'name'  => $property,
                'value' => $value,
            ];
        }

        return $identity;
    }

    /**
     * @param array $options
     *
     * @return Response
     */
    public function getRecentlyCreatedContacts($options = [])
    {
        $options += $this->itemsCount;

        return $this->client->setResource('recentlyAddedContacts')->read($options);
    }

    /**
     * @param string $type
     * @param array  $options
     *
     * @throws Exception\MetaException
     * @throws HubspotException
     * @throws JsonException
     *
     * @return HubspotHttpResponseService
     */
    public function pullContacts($type = 'all', $options = []): Response
    {
        $options += $this->itemsCount;
        if ('all' !== $type && !in_array($type, array_keys($this->allowedSyncContactOptions), true)) {
            throw new HubSpotException(sprintf('Sync Type %s not allowed', $type));
        }

        if ($type !== 'all') {
            $metaInfo = $this->meta->setName($type)->get();
            if (!empty($metaInfo)) {
                $options = array_merge(
                    $options,
                    [
                        'vidOffset'  => $metaInfo['vidOffset'],
                        'timeOffset' => $metaInfo['timeOffset'],
                    ]
                );
            }
        }

        /** @var Response $response */
        return $this->{$this->allowedSyncContactOptions[$type]}($options);
    }

    /**
     * @param $contactId
     *
     * @return HubspotHttpResponseService
     */
    public function getContactById($contactId): Response
    {
        return $this->client->setResource('getContactById')->read([], [$contactId]);
    }

    /**
     * @return HubspotHttpResponseService
     */
    public function getCompanies(): HubspotHttpResponseService
    {
        return $this->client->setResource('getCompanies')->read();
    }

    /**
     * @return HubspotHttpResponseService
     */
    public function getRecentCompanies(): HubspotHttpResponseService
    {
        return $this->client->setResource('getRecentCompanies')->read();
    }

    /**
     * @param int $companyId
     *
     * @return HubspotHttpResponseService
     */
    public function getCompanyById(int $companyId): HubspotHttpResponseService
    {
        return $this->client->setResource('getCompanyById')->read([], [$companyId]);
    }

    /**
     * @param string $type
     * @param array  $options
     *
     * @throws Exception\MetaException
     * @throws HubspotException
     * @throws JsonException
     *
     * @return mixed
     */
    public function pullCompanies($type = 'all', $options = [])
    {
        $options += $this->itemsCount;

        if (in_array($type, ['all', 'company'])) {
            $type = 'companies';
        }

        if (!in_array($type, array_keys($this->allowedSyncContactOptions), true)) {
            throw new HubSpotException(sprintf('Sync Type %s not allowed', $type));
        }

        if (($type !== 'companies') && $metaInfo = $this->meta->setName($type)->get()) {
            $options = array_merge(
                $options,
                [
                    'vidOffset'  => $metaInfo['vidOffset'],
                    'timeOffset' => $metaInfo['timeOffset'],
                ]
            );
        }

        /** @var Response $response */
        return $this->{$this->allowedSyncContactOptions[$type]}($options);
    }

    /**
     * @param int $companyId
     *
     * @return HubspotHttpResponseService
     */
    public function getCompanyContacts(int $companyId): HubspotHttpResponseService
    {
        return $this->client->setResource('getCompanyContacts')->read([], [$companyId]);
    }

    /**
     * @param int $companyId
     * @param int $contactId
     *
     * @return HubspotHttpResponseService
     */
    public function addContactToCompany(int $companyId, int $contactId): HubspotHttpResponseService
    {
        $data = [
            "fromObjectId" => $contactId,
            "toObjectId"   => $companyId,
            "category"     => 'HUBSPOT_DEFINED',
            "definitionId" => 1,
        ];

        return $this->client->setResource('getCompanyContacts')->put($data);
    }

    /**
     * @param int $companyId
     * @param int $contactId
     *
     * @return HubspotHttpResponseService
     */
    public function removeContactFromCompany(int $companyId, int $contactId): HubspotHttpResponseService
    {
        $data = [
            "fromObjectId" => $contactId,
            "toObjectId"   => $companyId,
            "category"     => 'HUBSPOT_DEFINED',
            "definitionId" => 1,
        ];

        return $this->client->setResource('deleteContactFromCompany')->put($data);
    }

    /**
     * @param int $ownerId
     *
     * @throws HubspotException
     *
     * @return HubspotHttpResponseService
     */
    public function getHubspotOwner(int $ownerId): HubspotHttpResponseService
    {
        return $this->client->apiCall('getHubspotOwner', 'get', [], [$ownerId]);
    }
}
