<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Service;

use Xentral\Modules\Pipedrive\Exception\PipedriveClientException;
use Xentral\Modules\Pipedrive\Exception\PipedriveConfigurationException;
use Xentral\Modules\Pipedrive\Exception\PipedriveHttpClientException;
use Xentral\Modules\Pipedrive\Exception\PipedriveMetaException;
use Xentral\Modules\Pipedrive\Exception\PipedrivePersonPropertyServiceException;

final class PipedrivePersonPropertyService
{

    /** @var PipedriveClientService $client */
    private $client;

    /**
     * @param PipedriveClientService $client
     */
    public function __construct(PipedriveClientService $client)
    {
        $this->client = $client;
    }

    /**
     * @throws PipedriveClientException
     * @throws PipedriveHttpClientException
     * @throws PipedriveConfigurationException
     * @throws PipedriveMetaException
     *
     * @return PipedriveServerResponseInterface
     */
    public function getProperties(): PipedriveServerResponseInterface
    {
        return $this->client->read('getPersonFields');
    }

    /**
     * @param int $id
     *
     * @throws PipedriveClientException
     * @throws PipedriveConfigurationException
     * @throws PipedriveHttpClientException
     * @throws PipedriveMetaException
     *
     * @return PipedriveServerResponseInterface
     */
    public function getProperty(int $id): PipedriveServerResponseInterface
    {
        return $this->client->read('getOnePersonField', [], [$id]);
    }

    /**
     * @throws PipedriveClientException
     * @throws PipedriveConfigurationException
     * @throws PipedriveHttpClientException
     * @throws PipedriveMetaException
     * @throws PipedrivePersonPropertyServiceException
     *
     * @return array
     */
    public function getPdLeadStatus(): array
    {
        $response = $this->getProperty(9039);
        if ($response->getStatusCode() !== 200) {
            throw new PipedrivePersonPropertyServiceException($response->getError());
        }

        if (($data = $response->getData()) && array_key_exists('options', $data)) {
            return $data['options'];
        }

        return [];
    }

}
