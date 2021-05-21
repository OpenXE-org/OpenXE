<?php

namespace Xentral\Modules\Hubspot;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use Xentral\Modules\Hubspot\HubspotHttpResponseService as Response;
use Xentral\Modules\Hubspot\Exception\HttpClientException;
use Xentral\Modules\Hubspot\Interfaces\HubspotHttpClientInterface;

final class HubspotHttpClientService implements HubspotHttpClientInterface
{
    /**
     * @var string
     */
    protected $endpoint = null;

    /**
     * @var array
     */
    protected $userAgent = [];

    /**
     * @var array
     */
    protected $hRequestVerbs = [
        self::GET_REQUEST    => null,
        self::POST_REQUEST   => 'json',
        self::PUT_REQUEST    => 'json',
        self::PATCH_REQUEST  => 'json',
        self::DELETE_REQUEST => null,
    ];

    /**
     * @var int
     */
    private $timeout;

    /**
     * @var array
     */
    private $_headers = [];

    /**
     * @param int $timeout > 0
     *
     */
    public function __construct($timeout = 0)
    {
        if (!is_int($timeout) || $timeout < 0) {
            throw new HttpClientException(
                sprintf(
                    'Connection timeout must be an int >= 0, got "%s".',
                    is_object($timeout) ? get_class($timeout) : gettype($timeout) . ' ' . var_export($timeout, true)
                )
            );
        }
        if (!empty($timeout)) {
            $this->timeout = $timeout;
        }
    }

    /**
     * @param string $url
     * @param string $method
     * @param array  $data
     *
     * @param array  $headers
     *
     * @return HubspotHttpResponseService
     * @throws HttpClientException
     */
    public function performRequest($url, $method, array $data = [], $headers = [])
    {
        $this->setHeader($headers);

        $hHeaders = $this->getHeaders();

        try {
            $client = $this->getClient();

            $keyParam = $this->hRequestVerbs[$method];
            $paramData = ['headers' => $hHeaders];
            if ($keyParam !== null) {
                $paramData[$keyParam] = $data;
            }

            /** @var Response */
            $response = $client->request($method, $url, $paramData);

            return new HubspotHttpResponseService($response);
        } catch (RequestException $exception) {
            throw new HttpClientException($exception->getMessage());
        } catch (GuzzleException $exception) {
            throw new HttpClientException($exception->getMessage());
        }
    }

    /**
     * @return Client
     */
    protected function getClient()
    {
        return new Client(['timeout' => $this->timeout]);
    }

    /**
     * @param string $url
     * @param array  $data
     * @param array  $header
     *
     * @return Response
     */
    public function get($url, $data = [], $header = [])
    {
        if (!empty($data)) {
            $query = parse_url($url, PHP_URL_QUERY);
            $newQuery = http_build_query($data);
            $url = $query ? $url . '&' . $newQuery : $url . '?' . $newQuery;
        }

        return $this->performRequest($url, static::GET_REQUEST, [], $header);
    }

    /**
     * @param string $url
     * @param array  $data
     * @param array  $header
     *
     * @return Response
     */
    public function post($url, $data = [], $header = [])
    {
        $defHeader = ['Content-Type' => 'application/json', 'Accept' => 'application/json'];
        $header += $defHeader;

        return $this->performRequest($url, static::POST_REQUEST, $data, $header);
    }

    /**
     * @param string $url
     * @param array  $data
     * @param array  $header
     *
     * @return Response
     */
    public function patch($url, $data = [], $header = [])
    {
        return $this->performRequest($url, static::PATCH_REQUEST, $data, $header);
    }

    /**
     * @param string $url
     * @param array  $data
     * @param array  $header
     *
     * @return Response
     */
    public function delete($url, $data = [], $header = [])
    {
        return $this->performRequest($url, static::DELETE_REQUEST, $data, $header);
    }

    /**
     * @param array $option
     */
    protected function setHeader($option = [])
    {
        $this->_headers += $option;
    }

    /**
     * @return array
     */
    protected function getHeaders()
    {
        $default = ['User-Agent' => 'Xentral-ERP-CRM'];

        return $this->_headers += $default;
    }

    /**
     * @param string $url
     * @param array  $data
     * @param array  $header
     *
     * @return Response
     */
    public function put($url, $data = [], $header = [])
    {
        $defHeader = ['Content-Type' => 'application/json', 'Accept' => 'application/json'];
        $header += $defHeader;

        return $this->performRequest($url, static::PUT_REQUEST, $data, $header);
    }

}
