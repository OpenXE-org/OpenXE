<?php

declare(strict_types=1);

namespace Xentral\Modules\Hubspot;

use Xentral\Modules\Hubspot\Exception\HubspotEngagementException;

final class HubspotEngagementService
{

    /** @var HubspotClientService $client */
    private $client;

    /** @var string[] */
    private const ASSOCIATIONS_VALIDATOR = [
        'contact' => 'contactIds',
        'company' => 'companyIds',
        'deal'    => 'dealIds',
        'owner'   => 'ownerIds',
        'ticket'  => 'ticketIds',
    ];

    /** @var string[] */
    private const ALLOWED_TYPES = ['EMAIL', 'CALL', 'MEETING', 'TASK', 'NOTE'];

    /**
     * @param HubspotClientService $client
     */
    public function __construct(HubspotClientService $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $body
     * @param array  $associationIds
     * @param string $type
     *
     * @return HubspotHttpResponseService|null
     */
    public function createCompanyEngagement(
        string $body,
        array $associationIds = [],
        string $type = 'NOTE'
    ): ?HubspotHttpResponseService {
        try {
            $data = $this->getCreateData($type, $body, 'company', $associationIds);
            $response = $this->client->apiCall('createEngagement', 'post', $data);
        } catch (Exception\HubspotException | HubspotEngagementException $e) {
            return null;
        }

        return $response;
    }

    /**
     * @param string $body
     * @param array  $associationIds
     * @param string $type
     *
     * @return HubspotHttpResponseService|null
     */
    public function createContactEngagement(
        string $body,
        array $associationIds = [],
        string $type = 'NOTE'
    ): ?HubspotHttpResponseService {
        try {
            $data = $this->getCreateData($type, $body, 'contact', $associationIds);
            $response = $this->client->apiCall('createEngagement', 'post', $data);
        } catch (Exception\HubspotException | HubspotEngagementException  $e) {
            return null;
        }

        return $response;
    }

    /**
     * @param string $body
     * @param array  $associationIds
     * @param string $type
     *
     * @return HubspotHttpResponseService|null
     */
    public function createDealEngagement(
        string $body,
        array $associationIds = [],
        string $type = 'NOTE'
    ): ?HubspotHttpResponseService {
        try {
            $data = $this->getCreateData($type, $body, 'deal', $associationIds);
            $response = $this->client->apiCall('createEngagement', 'post', $data);
        } catch (Exception\HubspotException | HubspotEngagementException $e) {
            return null;
        }

        return $response;
    }

    /**
     * @param int    $engagementId
     * @param string $body
     * @param string $intendedTo
     * @param string $type
     *
     * @return bool
     */
    public function updateEngagement(int $engagementId, string $body, string $intendedTo, string $type = 'NOTE'): bool
    {
        try {
            $data = $this->getCreateData($type, $body, $intendedTo);
            $response = $this->client->apiCall('updateEngagement', 'patch', $data, [$engagementId]);
        } catch (Exception\HubspotException | HubspotEngagementException $e) {
            return false;
        }

        return $response->getStatusCode() === 200;
    }

    /**
     * @param array $options
     *
     * @throws Exception\HubspotException
     *
     * @return HubspotHttpResponseService
     */
    public function getRecentEngagements(array $options = []): HubspotHttpResponseService
    {
       if (array_key_exists('offset', $options) && array_key_exists('since', $options)) {
           unset($options['since'], $options['offset']);
       }
       
       return $this->client->apiCall('getRecentEngagements', 'get', $options);
    }

    /**
     * @param string $type
     * @param string $body
     * @param string $intendedTo
     * @param array  $associationIds
     *
     * @throws HubspotEngagementException
     *
     * @return array
     */
    private function getCreateData(string $type, string $body, string $intendedTo, array $associationIds = []): array
    {
        if (!in_array($type, self::ALLOWED_TYPES)) {
            throw new HubspotEngagementException(sprintf('Type %s is not allowed !', $type));
        }

        if (!array_key_exists($intendedTo, self::ASSOCIATIONS_VALIDATOR)) {
            throw new HubspotEngagementException(sprintf('Intended Association %s is not allowed !', $intendedTo));
        }

        $data = [
            'engagement' => [
                'active'    => true,
                'type'      => $type,
                'timestamp' => time() * 1000,
            ],
        ];

        if (!empty($body)) {
            $data['metadata'] = ['body' => $body];
        }

        if (!empty($associationIds)) {
            $data['associations'] = [
                self::ASSOCIATIONS_VALIDATOR[$intendedTo] => $associationIds,
            ];
        }

        return $data;
    }
}
