<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleApi\Client;

use Xentral\Components\HttpClient\Exception\TransferErrorExceptionInterface;
use Xentral\Components\HttpClient\HttpClientInterface;
use Xentral\Components\HttpClient\Request\ClientRequest;
use Xentral\Components\Logger\LoggerAwareTrait;
use Xentral\Components\Util\StringUtil;
use Xentral\Modules\GoogleApi\Data\GoogleAccountData;
use Xentral\Modules\GoogleApi\Exception\GoogleApiRequestException;
use Xentral\Modules\GoogleApi\Exception\GoogleApiResponseException;

final class GoogleApiClient implements GoolgeApiClientInterface
{
    use LoggerAwareTrait;

    /** @var HttpClientInterface $httpClient */
    private $httpClient;

    /** @var GoogleAccountData $account */
    private $account;

    /**
     * @param HttpClientInterface $client
     * @param GoogleAccountData   $account
     */
    public function __construct(HttpClientInterface $client, GoogleAccountData $account)
    {
        $this->httpClient = $client;
        $this->account = $account;
    }

    /**
     * @return GoogleAccountData
     */
    public function getAccount(): GoogleAccountData
    {
        return $this->account;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $data
     * @param array  $headers
     *
     * @throws GoogleApiRequestException
     * @throws GoogleApiResponseException
     *
     * @return array
     */
    public function sendRequest(
        string $method,
        string $uri,
        array $data = null,
        array $headers = []
    ): array {
        $requestBody = null;
        if ($data !== null && is_array($data) && count($data) > 0) {
            $headers['Content-Type'] = 'application/json';
            $requestBody = json_encode($data);
        }

        $request = new ClientRequest($method, $uri, $headers, $requestBody);
        try {
            $response = $this->httpClient->sendRequest($request);
            $this->logger->debug(
                'Google API request succeeded: {uri}',
                ['uri' => $request->getUri(), 'request' => $request, 'response' => $response]
            );
        } catch (TransferErrorExceptionInterface $e) {
            $this->logger->warning(
                'Google API request failed: {uri} ERROR {code}',
                [
                    'uri'      => $request->getUri(),
                    'code'     => $e->getCode(),
                    'request'  => $request,
                    'response' => $e->getResponse(),
                ]
            );
            throw new GoogleApiRequestException($e->getMessage(), $e->getCode(), $e);
        }

        $result = [];

        $contentType = mb_strtolower($response->getHeaderLine('content-type'));
        $responseBody = $response->getBody()->getContents();
        if ($responseBody !== '' && StringUtil::startsWith($contentType, 'application/json')) {
            $result = json_decode($responseBody, true);
        }

        if ($result === false || $result === null || !is_array($result)) {
            throw new GoogleApiResponseException('Wrong format in JSON response.');
        }

        return $result;
    }
}
