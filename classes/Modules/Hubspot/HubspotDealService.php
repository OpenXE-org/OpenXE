<?php

namespace Xentral\Modules\Hubspot;

use Xentral\Modules\Hubspot\Exception\HubspotException;
use Xentral\Modules\Hubspot\HubspotHttpResponseService as Response;
use Xentral\Modules\Hubspot\Validators\DealValidator;

final class HubspotDealService
{
    /** @var array $allowedSyncDealsOptions */
    private $allowedSyncDealsOptions = [
        'recently_created_deals' => 'getRecentlyCreatedDeals',
        'recently_updated_deals' => 'getRecentlyUpdatedDeals',
        'all_deals'              => 'getDeals',
    ];

    /** @var array $asDealPhases */
    private $asDealPhases = [
        'appointmentscheduled',
        'qualifiedtobuy',
        'presentationscheduled',
        'decisionmakerboughtin',
        'contractsent',
        'closedwon',
        'closedlost',
    ];

    private $limit = ['limit' => 200];

    /** @var HubspotClientService $client */
    private $client;

    /** @var HubspotMetaService $meta */
    private $meta;

    /** @var DealValidator $validator */
    private $validator;

    public function __construct(
        HubspotClientService $client,
        HubspotMetaService $meta,
        DealValidator $validator
    ) {
        $this->client = $client;
        $this->meta = $meta;
        $this->validator = $validator;
    }

    public function createDeal($data = [])
    {
        if (!$this->validator->isValid($data)) {
            throw new HubSpotException(sprintf('Invalid Deal data'));
        }
        $default = ['pipeline' => 'default', 'dealstage' => 'appointmentscheduled'];
        $data += $default;

        if (!$this->validator->isValid($data)) {
            throw new HubSpotException(sprintf('Invalid Deal data'));
        }

        $deal = $this->formatDealData($this->validator->getData());

        return $this->client->setResource('createDeal')->post($deal);
    }

    public function getDealById($dealId)
    {
        return $this->client->setResource('getDealById')->read([], [$dealId]);
    }

    /**
     * @param array $options
     *
     * @return Response
     */
    public function getDeals($options = [])
    {
        $options += $this->limit;

        return $this->client->setResource('allDeals')->read($options);
    }

    /**
     * @param array $options
     *
     * @return Response
     */
    public function getRecentlyUpdatedDeals($options = [])
    {
        $options += $this->limit;

        return $this->client->setResource('recentlyUpdatedDeals')->read($options);
    }

    /**
     * @param int $dealId
     *
     * @return Response
     */
    public function deleteDeal($dealId)
    {
        return $this->client->setResource('deleteDeal')->delete([], [$dealId]);
    }

    /**
     * @param int   $dealId
     * @param array $data
     *
     * @return HubspotHttpResponseService
     */
    public function updateDealById($dealId, $data = [])
    {
        if (!$this->validator->isValid($data)) {
            throw new HubSpotException(sprintf('Invalid Deal data'));
        }

        $deal = $this->formatDealData($this->validator->getData());

        return $this->client->setResource('updateDeal')->put($deal, [$dealId]);
    }


    /**
     * @param array $options
     *
     * @return Response
     */
    public function getRecentlyCreatedDeals($options = [])
    {
        $options += $this->limit;

        return $this->client->setResource('recentlyAddedDeals')->read($options);
    }

    /**
     * @param string $type
     * @param array  $options
     *
     * @return mixed
     */
    public function pullDeals($type = 'all', $options = [])
    {
        if ('all' !== $type && !in_array($type, array_keys($this->allowedSyncDealsOptions), true)) {
            throw new HubSpotException(sprintf('Sync Deal with Type %s not allowed', $type));
        }

        if (($type !== 'all') && ($metaInfo = $this->meta->setName($type)->get())) {
            $since = !array_key_exists('since', $metaInfo) ? time() * 1000 : $metaInfo['since'];
            $options += ['offset' => $metaInfo['offset'], 'since' => $since];
        }

        /** @var Response $response */
        return $this->{$this->allowedSyncDealsOptions[$type]}($options);
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    private function formatDealData($data)
    {
        $identity['properties'] = [];
        foreach ($data as $property => $value) {
            $identity['properties'][] = [
                'name'  => $property,
                'value' => $value,
            ];
        }

        return $identity;
    }
}
