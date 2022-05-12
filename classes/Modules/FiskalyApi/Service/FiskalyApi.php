<?php

namespace Xentral\Modules\FiskalyApi\Service;

use Exception;
use FiskalyClient\errors\exceptions\FiskalyClientException;
use FiskalyClient\errors\exceptions\FiskalyHttpException;
use FiskalyClient\errors\exceptions\FiskalyHttpTimeoutException;
use FiskalyClient\FiskalyClient;
use FiskalyClient\responses\SelfTestResponse;
use Xentral\Components\HttpClient\Exception\ClientErrorException;
use Xentral\Modules\FiskalyApi\Data\TechnicalSecuritySystem;
use Xentral\Modules\FiskalyApi\Data\Client;
use Xentral\Modules\FiskalyApi\Exception\InvalidCredentialsException;
use Xentral\Modules\FiskalyApi\Exception\InvalidTransactionException;
use Xentral\Modules\FiskalyApi\Exception\SmaEndpointNotFoundException;
use Xentral\Modules\FiskalyApi\Exception\SmaEndpointNotReachableException;
use Xentral\Modules\FiskalyApi\Transaction\Transaction;

/**
 * Class FiskalyApi
 *
 * @package Xentral\Modules\FiskalyApi\Service
 */
class FiskalyApi
{
    /** @var string */
    private $apiKey;

    /** @var string */
    private $apiSecret;

    /** @var string */
    private $accessToken = null;

    /** @var FiskalyClient */
    private $fiskalyClient;

    const DEFAULT_SMA_ENDPOINT = 'http://localhost:8080/invoke';

    /**
     * FiskalyApi constructor.
     *
     * @param string $smaEndpoint
     * @param string $apiKey
     * @param string $apiSecret
     * @param string $endpoint
     *
     * @throws Exception
     */
    public function __construct(string $smaEndpoint, string $apiKey, string $apiSecret, string $endpoint)
    {
        if(empty($smaEndpoint)) {
            $smaEndpoint = self::DEFAULT_SMA_ENDPOINT;
        }
        try {
            $this->fiskalyClient = FiskalyClient::createUsingCredentials(
                $smaEndpoint,
                $apiKey,
                $apiSecret,
                $endpoint
            );
        }
        catch (Exception $e) {
            if(strpos($e->getMessage(), '404') === 0) {
                throw new SmaEndpointNotFoundException($e->getMessage());
            }
            if($e->getMessage() === 'Undefined variable: http_response_header') {
                throw new SmaEndpointNotReachableException($e->getMessage());
            }
            throw $e;
        }
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }


    /**
     * @param string $apiKey
     * @param string $apiSecret
     *
     * @throws ClientErrorException
     *
     * @return string
     */
    protected function generateAccessToken(string $apiKey, string $apiSecret): string
    {
        $result = $this->callApiPost(
            'auth',
            json_encode(
                [
                    'api_key'    => $apiKey,
                    'api_secret' => $apiSecret,
                ]
            ),
            false
        );

        return $result->access_token;
    }


    /**
     * @param      $endpoint
     * @param null $body
     * @param null $query
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @return mixed
     */
    protected function callApiGet($endpoint, $body = null, $query = null)
    {
        return $this->callApi('GET', $endpoint, $body, $query);
    }

    /**
     * @param      $endpoint
     * @param null $body
     * @param null $query
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @return mixed
     */
    protected function callApiPost($endpoint, $body = null, $query = null)
    {
        return $this->callApi('POST', $endpoint, $body, $query);
    }

    /**
     * @param      $endpoint
     * @param null $body
     * @param null $query
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @return mixed
     */
    protected function callApiPut($endpoint, $body = null, $query = null)
    {
        return $this->callApi('PUT', $endpoint, $body, $query);
    }

    /**
     * @param      $method
     * @param      $endpoint
     * @param null $body
     * @param null $query
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpTimeoutException
     * @return mixed
     */
    private function callApi($method, $endpoint, $body = null, $query = null)
    {
        if (!empty($body)) {
            $body = base64_encode($body);
        }
        try {
            $response = $this->fiskalyClient->request(
                $method,
                $endpoint,
                $query,
                null,
                $body
            );

            return json_decode(base64_decode($response->getResponse()['body']));
        } catch (ClientErrorException | FiskalyHttpException $e) {
            $this->handleClientException($e);
        }
    }

    /**
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @return SelfTestResponse
     */
    public function selfTest(): SelfTestResponse
    {
        return $this->fiskalyClient->selfTest();
    }

    /**
     * @param Exception $e
     *
     * @throws Exception
     * @return void
     */
    private function handleClientException(Exception $e): void
    {
        if ($e->getStatus() === 401 || $e->getCode() == 401) {
            throw new InvalidCredentialsException('Falsche Zugangsdaten');
        }
        if ($e->getStatus() === 403 || $e->getCode() == 403) {
            throw new InvalidCredentialsException('Nutzer nicht berechtigt');
        }

        throw $e;
    }
}
