<?php

namespace Xentral\Modules\Hubspot;

use Xentral\Modules\Hubspot\Exception\HttpClientException;
use Xentral\Modules\Hubspot\Exception\HubspotException;

final class HubspotClientService
{

    private $resource;

    private $endPoints = [
        'allContacts'              => '/contacts/v1/lists/all/contacts/all',
        'recentlyAddedContacts'    => '/contacts/v1/lists/all/contacts/recent',
        'recentlyUpdatedContacts'  => '/contacts/v1/lists/recently_updated/contacts/recent',
        'deleteContact'            => '/contacts/v1/contact/vid/:contact_id',
        'updateContact'            => '/contacts/v1/contact/vid/:vid/profile',
        'createContact'            => '/contacts/v1/contact',
        'getContactById'           => '/contacts/v1/contact/vid/:vid/profile',
        'createOrUpdateContact'    => '/contacts/v1/contact/createOrUpdate/email/:contact_email',
        'createDeal'               => '/deals/v1/deal/',
        'recentlyUpdatedDeals'     => '/deals/v1/deal/recent/modified',
        'recentlyAddedDeals'       => '/deals/v1/deal/recent/created',
        'allDeals'                 => '/deals/v1/deal/paged',
        'deleteDeal'               => '/deals/v1/deal/:dealId',
        'updateDeal'               => '/deals/v1/deal/:dealId',
        'getAllContactProperties'  => '/properties/v1/contacts/properties',
        'getContactProperty'       => '/properties/v1/contacts/properties/named/:property_name',
        'getCompanyProperty'       => '/properties/v1/companies/properties/named/:property_name',
        'getPipelineProperties'    => '/crm-pipelines/v1/pipelines/:object_type',
        'getDealById'              => '/deals/v1/deal/:dealId',
        'getCompanyById'           => '/companies/v2/companies/:companyId',
        'getRecentCompanies'       => '/companies/v2/companies/recent/modified',
        'getCompanies'             => '/companies/v2/companies/paged?properties=name&properties=website&properties=country&properties=zip&properties=address&properties=hs_lead_status&properties=city&properties=phone',
        'getCompanyContacts'       => '/companies/v2/companies/:companyId/contacts',
        'addContactToCompany'      => '/crm-associations/v1/associations',
        'deleteContactFromCompany' => '/crm-associations/v1/associations/delete',
        'createCompany'            => '/companies/v2/companies',
        'updateCompany'            => '/companies/v2/companies/:companyId',
        'createEngagement'         => '/engagements/v1/engagements',
        'updateEngagement'         => '/engagements/v1/engagements/:engagementId',
        'getRecentEngagements'     => '/engagements/v1/engagements/recent/modified',
        'getHubspotOwner'          => '/owners/v2/owners/:ownerId',

    ];

    private $authMethod = 'key';

    /** @var string|null $apiKey */
    private $apiKey;

    /** @var HubspotHttpClientService $client */
    private $client;
    /**
     * @var HubspotConfigurationService
     */
    private $confService;

    public function __construct(HubspotHttpClientService $client, HubspotConfigurationService $confService, $apiKey = null)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->confService = $confService;
    }

    /** @var string $apiUrl */
    private $apiUrl = 'https://api.hubapi.com%s';

    /**
     * @param null  $suffix
     * @param array $args
     *
     * @throws HubspotException
     *
     * @return string
     */
    public function getEndPoint($suffix = null, $args = [])
    {
        $resource = null;
        if (null === $suffix && !($resource = $this->getResource())) {
            throw new HubspotException('Endpoint suffix cannot be set');
        }

        if ($resource !== null && !array_key_exists($resource, $this->endPoints)) {
            throw new HubspotException('Undefined resource endpoint');
        }

        $suffixUrl = null === $suffix ? $this->endPoints[$resource] : $suffix;
        $url = sprintf($this->apiUrl, $suffixUrl);
        if ($this->authMethod === 'key') {
            $apiKey = null === $this->apiKey ? $this->getConfApiKey() : $this->apiKey;
            $url .=   strpos($url, '?') === false? '?hapikey=' . $apiKey : '&hapikey=' . $apiKey;
        }
        if (!empty($args)) {
            preg_match_all('/:[a-zA-Z0-9._-]+/', $url, $match);
            if (!empty($match) && !empty($match[0])) {
                $url = str_replace($match[0], $args, $url);
            }
        }

        return $url;
    }

    /**
     * @param $resource
     *
     * @return HubspotClientService
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param array $data
     *
     * @param array $endPointArgs
     *
     * @return HubspotHttpResponseService
     */
    public function read($data = [], $endPointArgs = [])
    {
        return $this->client->get($this->getEndPoint(null, $endPointArgs), $data);
    }

    /**
     * @return false|string|null
     */
    private function getConfApiKey()
    {
        return $this->confService->getDecryptedConfiguration(HubspotConfigurationService::HUBSPOT_SALT_CONF_NAME);
    }

    /**
     * @param array $data
     *
     * @param array $endPointArgs
     *
     * @return HubspotHttpResponseService
     */
    public function post($data = [], $endPointArgs = [])
    {
        return $this->client->post($this->getEndPoint(null, $endPointArgs), $data);
    }

    /**
     * @param array $data
     *
     * @param array $endPointArgs
     *
     * @return HubspotHttpResponseService
     */
    public function delete($data = [], $endPointArgs = [])
    {
        return $this->client->delete($this->getEndPoint(null, $endPointArgs), $data);
    }

    /**
     * @param array $data
     *
     * @param array $endPointArgs
     *
     * @return HubspotHttpResponseService
     */
    public function put($data = [], $endPointArgs = [])
    {
        return $this->client->put($this->getEndPoint(null, $endPointArgs), $data);
    }

    /**
     * @param array $data
     * @param array $endPointArgs
     *
     * @throws HubspotException
     *
     * @return HubspotHttpResponseService
     */
    public function patch($data = [], $endPointArgs = []): HubspotHttpResponseService
    {
        return $this->client->patch($this->getEndPoint(null, $endPointArgs), $data);
    }

    /**
     * @param array $data
     * @param array $endPointArgs
     *
     * @return HubspotHttpResponseService
     */
    public function get($data = [], $endPointArgs = []): HubspotHttpResponseService
    {
        return $this->read($data, $endPointArgs);
    }

    /**
     * @param string $resource
     * @param string $type
     * @param array  $data
     * @param array  $endPointArgs
     *
     * @throws HubspotException
     *
     * @return HubspotHttpResponseService
     */
    public function apiCall(
        string $resource,
        string $type,
        array $data = [],
        array $endPointArgs = []
    ) : HubspotHttpResponseService
    {
        if (!method_exists($this, $type)) {
            throw new HttpClientException(sprintf('Methode ::%s is not yet implemented !', $type));
        }
        $this->resource = $resource;
        return $this->client->{strtolower($type)}($this->getEndPoint(null, $endPointArgs), $data);
    }
}
