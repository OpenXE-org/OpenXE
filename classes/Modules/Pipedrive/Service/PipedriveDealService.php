<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Service;

use Xentral\Modules\Pipedrive\Exception\PipedriveClientException;
use Xentral\Modules\Pipedrive\Exception\PipedriveConfigurationException;
use Xentral\Modules\Pipedrive\Exception\PipedriveDealServiceException;
use Xentral\Modules\Pipedrive\Exception\PipedriveHttpClientException;
use Xentral\Modules\Pipedrive\Exception\PipedriveMetaException;
use Xentral\Modules\Pipedrive\Exception\PipedriveValidatorException;
use Xentral\Modules\Pipedrive\Validator\PipedriveDealValidator;

final class PipedriveDealService
{
    /** @var array $allowedSyncDealsOptions */
    private $allowedSyncDealsOptions = [
        'pipedrive_recently_updated_deals' => 'getRecentlyUpdatedDeals',
        'pipedrive_all_deals'              => 'getDeals',
    ];

    /** @var array $itemOption */
    private $itemOption = [
        'limit'           => 100,
        'items'           => 'deal',
        'start'           => 0,
        'since_timestamp' => '1970-01-01 23:59:59',
    ];

    /** @var PipedriveClientService $client */
    private $client;

    /** @var PipedriveDealValidator $validator */
    private $validator;

    /** @var PipedriveMetaReaderService $metaReaderService */
    private $metaReaderService;

    /**
     * @param PipedriveClientService     $client
     * @param PipedriveDealValidator     $validator
     * @param PipedriveMetaReaderService $metaReaderService
     */
    public function __construct(
        PipedriveClientService $client,
        PipedriveDealValidator $validator,
        PipedriveMetaReaderService $metaReaderService
    ) {
        $this->client = $client;
        $this->validator = $validator;
        $this->metaReaderService = $metaReaderService;
    }

    /**
     * @param array $data
     *
     * @throws PipedriveClientException
     * @throws PipedriveDealServiceException
     * @throws PipedriveHttpClientException
     * @throws PipedriveMetaException
     * @throws PipedriveValidatorException
     * @throws PipedriveConfigurationException
     *
     * @return PipedriveServerResponseInterface
     */
    public function createDeal(array $data = []): PipedriveServerResponseInterface
    {
        $default = ['status' => 'open', 'stage_id' => 1];
        $data += $default;

        if (!$this->validator->isValid($data)) {
            throw new PipedriveDealServiceException(sprintf('%s::createDeal Invalid Deal data', get_class($this)));
        }

        $deal = $this->validator->getData();

        return $this->client->post('createDeal', $deal);
    }

    /**
     * @param int $dealId
     *
     * @throws PipedriveClientException
     * @throws PipedriveConfigurationException
     * @throws PipedriveHttpClientException
     * @throws PipedriveMetaException
     *
     * @return PipedriveServerResponseInterface
     */
    public function getDealById(int $dealId): PipedriveServerResponseInterface
    {
        return $this->client->read('getDealById', [], [$dealId]);
    }

    /**
     * @param array $options
     *
     * @throws PipedriveClientException
     * @throws PipedriveConfigurationException
     * @throws PipedriveHttpClientException
     * @throws PipedriveMetaException
     *
     * @return PipedriveServerResponseInterface
     */
    public function getDeals(array $options = []): PipedriveServerResponseInterface
    {
        $options += $this->itemOption;

        return $this->client->read('allDeals', $options);
    }

    /**
     * @param array $options
     *
     * @throws PipedriveClientException
     * @throws PipedriveConfigurationException
     * @throws PipedriveHttpClientException
     * @throws PipedriveMetaException
     *
     * @return PipedriveServerResponseInterface
     */
    public function getRecentlyUpdatedDeals(array $options = []): PipedriveServerResponseInterface
    {
        $options += $this->itemOption;

        return $this->client->read('recentlyUpdatedDeals', $options);
    }

    /**
     * @param int $dealId
     *
     * @throws PipedriveClientException
     * @throws PipedriveConfigurationException
     * @throws PipedriveHttpClientException
     * @throws PipedriveMetaException
     *
     * @return PipedriveServerResponseInterface
     */
    public function deleteDeal(int $dealId): PipedriveServerResponseInterface
    {
        return $this->client->delete('deleteDeal', [$dealId]);
    }

    /**
     * @param int   $dealId
     * @param array $data
     *
     * @throws PipedriveClientException
     * @throws PipedriveConfigurationException
     * @throws PipedriveDealServiceException
     * @throws PipedriveHttpClientException
     * @throws PipedriveMetaException
     * @throws PipedriveValidatorException
     *
     * @return PipedriveServerResponseInterface
     */
    public function updateDealById(int $dealId, array $data = []): PipedriveServerResponseInterface
    {
        if (!$this->validator->isValid($data)) {
            throw new PipedriveDealServiceException(sprintf('%s::updateDealById Invalid Deal data', get_class($this)));
        }

        $deal = $this->validator->getData();

        return $this->client->put('updateDeal', $deal, [$dealId]);
    }

    /**
     * @param string $type
     * @param array  $options
     *
     * @throws PipedriveDealServiceException
     * @throws PipedriveMetaException
     *
     * @return PipedriveServerResponseInterface
     */
    public function pullDeals(
        string $type = 'pipedrive_recently_updated_deals',
        array $options = []
    ): PipedriveServerResponseInterface {
        if ('pipedrive_all_deals' !== $type && !array_key_exists($type, $this->allowedSyncDealsOptions)) {
            throw new PipedriveDealServiceException(sprintf('Sync Type %s not allowed', $type));
        }
        $metaFile = sprintf('%s.json', $type);
        if ($type !== 'pipedrive_all_deals') {
            $options = $this->addMetaOption($metaFile, $options);
        }

        /** @var PipedriveServerResponseInterface $response */
        return $this->{$this->allowedSyncDealsOptions[$type]}($options);
    }

    /**
     * @param string $metaFile
     * @param array  $options
     *
     * @throws PipedriveMetaException
     *
     * @return array
     */
    private function addMetaOption(string $metaFile, array $options) : array
    {
        $metaInfo = $this->metaReaderService->readFromFile($metaFile);

        if (!empty($metaInfo)) {
            $timeOffset = $metaInfo['timeOffset'];
            $offset = strtotime($metaInfo['timeOffset']);
            if ($offset !== false) {
                $timeOffset = gmdate('Y-m-d H:i:s', $offset - 3600);
            }
            $options = array_merge($options, ['since_timestamp' => $timeOffset]);
        }
        return  $options;
    }
}
