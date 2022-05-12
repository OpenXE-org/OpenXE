<?php

declare(strict_types=1);

namespace Xentral\Components\HttpClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Xentral\Components\HttpClient\Exception\TransferErrorException;
use Xentral\Components\HttpClient\Exception\TransferErrorExceptionInterface;
use Xentral\Components\HttpClient\Request\ClientRequest;
use Xentral\Components\HttpClient\Request\ClientRequestInterface;
use Xentral\Components\HttpClient\Response\ServerResponse;
use Xentral\Components\HttpClient\Response\ServerResponseInterface;
use Xentral\Components\HttpClient\Stream\StreamInterface;
use Xentral\Components\HttpClient\Uri\UriInterface;

final class HttpClient implements HttpClientInterface
{
    /** @var RequestOptions $options */
    private $options;

    /**
     * @param RequestOptions|null $options
     */
    public function __construct(RequestOptions $options = null)
    {
        $this->options = $options === null ? new RequestOptions() : clone $options;
    }

    /**
     * @param string                               $method  HTTP method
     * @param string|UriInterface                  $uri     URI
     * @param array                                $headers Request headers
     * @param string|null|resource|StreamInterface $body    Request body
     * @param string                               $version Protocol version
     *
     * @throws TransferErrorExceptionInterface
     *
     * @return ServerResponseInterface
     */
    public function request($method, $uri, array $headers = [], $body = null, $version = '1.1'): ServerResponseInterface
    {
        $request = new ClientRequest($method, $uri, $headers, $body, $version);

        return $this->sendRequest($request);
    }

    /**
     * @param ClientRequestInterface $request
     * @param RequestOptions|null    $options
     *
     * @throws TransferErrorExceptionInterface
     *
     * @return ServerResponseInterface
     */
    public function sendRequest(
        ClientRequestInterface $request,
        RequestOptions $options = null
    ): ServerResponseInterface {
        $optionsArray = $options === null ? $this->options->toArray() : $options->toArray();

        try {
            $client = $this->createClient();
            $response = $client->send($request, $optionsArray);

            return ServerResponse::fromGuzzleResponse($response);
            //
        } catch (GuzzleException $exception) {
            throw TransferErrorException::fromGuzzleException($exception);
        }
    }

    /**
     * @return GuzzleClient
     */
    private function createClient(): GuzzleClient
    {
        return new GuzzleClient($this->options->toArray());
    }
}
