<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Service;

use Xentral\Modules\Pipedrive\Exception\PipedriveClientException;
use Xentral\Modules\Pipedrive\Exception\PipedriveConfigurationException;
use Xentral\Modules\Pipedrive\Exception\PipedriveHttpClientException;
use Xentral\Modules\Pipedrive\Exception\PipedriveMetaException;

final class PipedriveClientService
{

    /** @var string[] $endPoints */
    private $endPoints = [
        'allPersons'             => '/v1/persons',
        'recentlyUpdatedPersons' => '/v1/recents',
        'deleteContact'          => '/v1/persons/:id',
        'updateContact'          => '/v1/persons/:id',
        'createContact'          => '/v1/persons',
        'getContactById'         => '/v1/persons/:id',
        'recentlyUpdatedDeals'   => '/v1/recents',
        'createDeal'             => '/v1/deals',
        'allDeals'               => '/v1/deals',
        'deleteDeal'             => '/v1/deals/:id',
        'updateDeal'             => '/v1/deals/:id',
        'getDealById'            => '/v1/deals/:id',
        'getPersonFields'        => '/v1/personFields',
        'getOnePersonField'      => '/v1/personFields/:id',
        'getStages'              => '/v1/stages',
        'getPipelines'           => '/v1/pipelines',
    ];

    /** @var string $authMethod */
    private $authMethod = 'key';

    /** @var string|null $apiKey */
    private $apiKey;

    /** @var PipedriveHttpClientService $client */
    private $client;

    /** @var PipedriveConfigurationService $confService */
    private $confService;

    /**
     * @param PipedriveHttpClientService    $client
     * @param PipedriveConfigurationService $confService
     * @param string|null                   $apiKey
     */
    public function __construct(
        PipedriveHttpClientService $client,
        PipedriveConfigurationService $confService,
        ?string $apiKey = null
    ) {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->confService = $confService;
    }

    /** @var string $apiUrl */
    private $apiUrl = 'https://api.pipedrive.com%s';

    /**
     * @param string      $resource
     * @param array       $args
     * @param string|null $suffix
     *
     * @throws PipedriveClientException
     * @throws PipedriveConfigurationException
     * @throws PipedriveMetaException
     *
     * @return string
     */
    protected function getEndPoint(string $resource, array $args = [], ?string $suffix = null): string
    {

        if (!array_key_exists($resource, $this->endPoints)) {
            throw new PipedriveClientException('Undefined resource endpoint');
        }

        $suffixUrl = $suffix ?? $this->endPoints[$resource];
        $url = sprintf($this->apiUrl, $suffixUrl);
        if ($this->authMethod === 'key') {
            $apiKey = $this->apiKey ?? $this->getConfApiKey();
            $url .= sprintf('?api_token=%s', $apiKey);
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
     * @param string $ressource
     * @param array  $data
     * @param array  $endPointArgs
     *
     * @throws PipedriveClientException
     * @throws PipedriveConfigurationException
     * @throws PipedriveHttpClientException
     * @throws PipedriveMetaException
     *
     * @return PipedriveServerResponseInterface
     */
    public function read(string $ressource, array $data = [], array $endPointArgs = []): PipedriveServerResponseInterface
    {
        return $this->client->get($this->getEndPoint($ressource, $endPointArgs), $data);
    }

    /**
     * @throws PipedriveConfigurationException
     * @throws PipedriveMetaException
     *
     * @return string|null
     */
    protected function getConfApiKey(): ?string
    {
        return $this->confService->getDecryptedConfiguration();
    }

    /**
     * @param string $ressource
     * @param array  $data
     * @param array  $endPointArgs
     *
     * @throws PipedriveClientException
     * @throws PipedriveConfigurationException
     * @throws PipedriveHttpClientException
     * @throws PipedriveMetaException
     *
     * @return PipedriveServerResponseInterface
     */
    public function post(string $ressource, array $data = [], array $endPointArgs = []): PipedriveServerResponseInterface
    {
        return $this->client->post($this->getEndPoint($ressource, $endPointArgs), $data);
    }

    /**
     * @param string $ressource
     * @param array  $data
     * @param array  $endPointArgs
     *
     * @throws PipedriveClientException
     * @throws PipedriveConfigurationException
     * @throws PipedriveHttpClientException
     * @throws PipedriveMetaException
     *
     * @return PipedriveServerResponseInterface
     */
    public function delete(string $ressource, array $endPointArgs = [], array $data = []): PipedriveServerResponseInterface
    {
        return $this->client->delete($this->getEndPoint($ressource, $endPointArgs), $data);
    }

    /**
     * @param string $ressource
     * @param array  $data
     * @param array  $endPointArgs
     *
     * @throws PipedriveClientException
     * @throws PipedriveConfigurationException
     * @throws PipedriveHttpClientException
     * @throws PipedriveMetaException
     *
     * @return PipedriveServerResponseInterface
     */
    public function put(string $ressource, array $data = [], array $endPointArgs = []): PipedriveServerResponseInterface
    {
        return $this->client->put($this->getEndPoint($ressource, $endPointArgs), $data);
    }

}
