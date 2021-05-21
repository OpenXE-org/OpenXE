<?php

declare(strict_types=1);

namespace Xentral\Components\HttpClient;

use Xentral\Components\HttpClient\Exception\TransferErrorExceptionInterface;
use Xentral\Components\HttpClient\Request\ClientRequestInterface;
use Xentral\Components\HttpClient\Response\ServerResponseInterface;
use Xentral\Components\HttpClient\Stream\StreamInterface;
use Xentral\Components\HttpClient\Uri\UriInterface;

interface HttpClientInterface
{
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
    public function request(
        $method,
        $uri,
        array $headers = [],
        $body = null,
        $version = '1.1'
    ): ServerResponseInterface;

    /**
     * @param ClientRequestInterface $request
     * @param RequestOptions|null    $options
     *
     * @return ServerResponseInterface
     */
    public function sendRequest(
        ClientRequestInterface $request,
        RequestOptions $options = null
    ): ServerResponseInterface;
}
