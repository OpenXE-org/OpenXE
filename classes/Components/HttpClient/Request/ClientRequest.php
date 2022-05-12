<?php

declare(strict_types=1);

namespace Xentral\Components\HttpClient\Request;

use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;
use Xentral\Components\HttpClient\Uri\Uri;
use Xentral\Components\HttpClient\Uri\UriInterface;

final class ClientRequest extends GuzzleRequest implements ClientRequestInterface
{
    /**
     * @param PsrRequestInterface $guzzleRequest
     *
     * @return ClientRequestInterface
     */
    public static function fromGuzzleRequest(PsrRequestInterface $guzzleRequest): ClientRequestInterface
    {
        return new self(
            $guzzleRequest->getMethod(),
            Uri::fromGuzzleUri($guzzleRequest->getUri()),
            $guzzleRequest->getHeaders(),
            $guzzleRequest->getBody(),
            $guzzleRequest->getProtocolVersion()
        );
    }

    /**
     * @return UriInterface|string|void
     */
    public function getUri()
    {
        $guzzleUri = parent::getUri();

        return Uri::fromGuzzleUri($guzzleUri);
    }
}
