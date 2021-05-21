<?php

declare(strict_types=1);

namespace Xentral\Components\HttpClient\Response;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Xentral\Components\HttpClient\Exception\InvalidResponseException;
use Xentral\Components\HttpClient\Stream\StreamDecorator;

final class ServerResponse extends GuzzleResponse implements ServerResponseInterface
{
    /**
     * @param PsrResponseInterface $response
     *
     * @throws InvalidResponseException
     *
     * @return ServerResponseInterface
     */
    public static function fromGuzzleResponse(PsrResponseInterface $response): ServerResponseInterface
    {
        $resource = $response->getBody()->detach();
        if (!is_resource($resource)) {
            throw new InvalidResponseException('Response body is invalid.');
        }

        return new self(
            $response->getStatusCode(),
            $response->getHeaders(),
            new StreamDecorator($resource),
            $response->getProtocolVersion(),
            $response->getReasonPhrase()
        );
    }
}
