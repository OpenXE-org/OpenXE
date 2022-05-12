<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Service;

use Xentral\Components\HttpClient\Exception\TransferErrorExceptionInterface;
use Xentral\Components\HttpClient\HttpClientFactory;
use Xentral\Components\HttpClient\HttpClientInterface;
use Xentral\Components\HttpClient\Request\ClientRequest;
use \Xentral\Components\HttpClient\RequestOptions;
use Xentral\Modules\Pipedrive\Exception\PipedriveHttpClientException;

final class PipedriveHttpClientService
{

    /** @var string */
    private const GET_REQUEST = 'GET';
    /** @var string */
    private const POST_REQUEST = 'POST';
    /** @var string */
    private const DELETE_REQUEST = 'DELETE';
    /** @var string */
    private const PATCH_REQUEST = 'PATCH';
    /** @var string */
    private const PUT_REQUEST = 'PUT';

    /** @var null|string $endpoint */
    protected $endpoint;

    /** @var array $userAgent */
    protected $userAgent = [];

    /** @var array $hRequestVerbs */
    protected $hRequestVerbs = [
        self::GET_REQUEST    => null,
        self::POST_REQUEST   => 'json',
        self::PUT_REQUEST    => 'json',
        self::PATCH_REQUEST  => 'json',
        self::DELETE_REQUEST => null,
    ];

    /** @var int $timeout */
    private $timeout = 10;

    /** @var array $_headers */
    private $_headers = [];

    /** @var HttpClientInterface $client */
    private $client;

    /** @var null|RequestOptions $requestOption */
    private $requestOption;

    /** @var HttpClientFactory|null $factory */
    private $factory;

    /**
     * @param HttpClientFactory        $factory
     * @param int                      $timeout
     * @param HttpClientInterface|null $client
     *
     * @throws PipedriveHttpClientException
     */
    public function __construct(
        HttpClientFactory $factory,
        int $timeout = 0,
        ?HttpClientInterface $client = null
    ) {
        if (!empty($timeout)) {
            $this->timeout = $timeout;
        }

        $this->factory = $factory;
        $this->client = $client ?? $this->createClient();
    }

    /**
     * @param string $url
     * @param string $method
     * @param array  $data
     * @param array  $headers
     *
     * @throws PipedriveHttpClientException
     *
     * @return PipedriveServerResponseInterface|null
     */
    protected function performRequest(
        string $url,
        string $method,
        array $data = [],
        array $headers = []
    ): ?PipedriveServerResponseInterface {
        $this->setHeader($headers);

        $keyParam = $this->hRequestVerbs[$method];
        $paramData = null;
        if ($keyParam !== null && $keyParam === 'json') {
            $paramData = json_encode($data);
        }

        $rqHeaders = array_merge(
            ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
            $this->getHeaders()
        );

        $request = new ClientRequest($method, $url, $rqHeaders);
        if (!empty($paramData)) {
            if ($this->requestOption === null) {
                $this->requestOption = new RequestOptions();
            }
            $this->requestOption->setBody($paramData);
        }

        try {
            $response = $this->client->sendRequest($request, $this->requestOption);

            return new PipedriveHttpResponseService($response);
        } catch (TransferErrorExceptionInterface  $exception) {
            throw new PipedriveHttpClientException($exception->getMessage());
        }
    }

    /**
     * @throws PipedriveHttpClientException
     *
     * @return HttpClientInterface
     */
    private function createClient(): HttpClientInterface
    {
        if (!is_int($this->timeout) || $this->timeout < 0) {
            throw new PipedriveHttpClientException(
                sprintf('Connection timeout must be an int >= 0, got "%s".', gettype($this->timeout))
            );
        }

        if ($this->factory === null) {
            throw new PipedriveHttpClientException('HttpClientFactory is missing!');
        }

        $options = new RequestOptions();
        if ($this->timeout > 0) {
            $options->setTimeout($this->timeout);
        }
        $options->setHeader('Accept', 'application/json');
        $options->setHeader('Content-Type', 'application/json');

        $this->requestOption = $options;

        return $this->factory->createClient($this->requestOption);
    }

    /**
     * @param string $url
     * @param array  $data
     * @param array  $header
     *
     * @throws PipedriveHttpClientException
     *
     * @return PipedriveServerResponseInterface|null
     */
    public function get(string $url, array $data = [], array $header = []): ?PipedriveServerResponseInterface
    {
        if (!empty($data)) {
            $query = parse_url($url, PHP_URL_QUERY);
            $newQuery = http_build_query($data);
            $url = $query ? $url . '&' . $newQuery : $url . '?' . $newQuery;
        }

        return $this->performRequest($url, self::GET_REQUEST, [], $header);
    }

    /**
     * @param string $url
     * @param array  $data
     * @param array  $header
     *
     * @throws PipedriveHttpClientException
     *
     * @return PipedriveServerResponseInterface|null
     */
    public function post(string $url, array $data = [], array $header = []): ?PipedriveServerResponseInterface
    {
        return $this->performRequest($url, self::POST_REQUEST, $data, $header);
    }

    /**
     * @param string $url
     * @param array  $data
     * @param array  $header
     *
     * @throws PipedriveHttpClientException
     *
     * @return PipedriveServerResponseInterface|null
     */
    public function patch(string $url, array $data = [], array $header = []): ?PipedriveServerResponseInterface
    {
        return $this->performRequest($url, self::PATCH_REQUEST, $data, $header);
    }

    /**
     * @param string $url
     * @param array  $data
     * @param array  $header
     *
     * @throws PipedriveHttpClientException
     *
     * @return PipedriveServerResponseInterface|null
     */
    public function delete(string $url, array $data = [], array $header = []): ?PipedriveServerResponseInterface
    {
        return $this->performRequest($url, self::DELETE_REQUEST, $data, $header);
    }

    /**
     * @param array $option
     */
    protected function setHeader(array $option = []): void
    {
        $this->_headers += $option;
    }

    /**
     * @return array
     */
    protected function getHeaders(): array
    {
        $default = ['User-Agent' => 'Xentral-ERP-CRM'];

        return $this->_headers += $default;
    }

    /**
     * @param string $url
     * @param array  $data
     * @param array  $header
     *
     * @throws PipedriveHttpClientException
     *
     * @return PipedriveServerResponseInterface|null
     */
    public function put(string $url, array $data = [], array $header = []): ?PipedriveServerResponseInterface
    {
        return $this->performRequest($url, self::PUT_REQUEST, $data, $header);
    }
}
