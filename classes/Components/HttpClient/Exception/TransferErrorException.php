<?php

declare(strict_types=1);

namespace Xentral\Components\HttpClient\Exception;

use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use GuzzleHttp\Exception\ConnectException as GuzzleConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException as GuzzleServerException;
use GuzzleHttp\Exception\TooManyRedirectsException as GuzzleRedirectsException;
use RuntimeException;
use Throwable;
use Xentral\Components\HttpClient\Request\ClientRequest;
use Xentral\Components\HttpClient\Request\ClientRequestInterface;
use Xentral\Components\HttpClient\Response\ServerResponse;
use Xentral\Components\HttpClient\Response\ServerResponseInterface;

class TransferErrorException extends RuntimeException implements TransferErrorExceptionInterface
{
    /** @var ClientRequestInterface $request */
    protected $request;

    /** @var ServerResponseInterface|null $response */
    protected $response;

    /**
     * @param string                       $message
     * @param int                          $code
     * @param Throwable|null               $previous
     * @param ClientRequestInterface|null  $request
     * @param ServerResponseInterface|null $response
     */
    public function __construct(
        $message = '',
        $code = 0,
        Throwable $previous = null,
        ClientRequestInterface $request = null,
        ServerResponseInterface $response = null
    ) {
        parent::__construct($message, $code, $previous);

        if ($request !== null) {
            $this->request = $request;
        }
        if ($response !== null) {
            $this->response = $response;
        }
    }

    /**
     * @param GuzzleException $exception
     *
     * @return TransferErrorExceptionInterface
     */
    public static function fromGuzzleException(GuzzleException $exception): TransferErrorExceptionInterface
    {
        switch (get_class($exception)) {
            case GuzzleConnectException::class:
                $exceptionClass = ConnectionFailedException::class;
                break;
            case GuzzleClientException::class: // HTTP 4xx
                $exceptionClass = ClientErrorException::class;
                break;
            case GuzzleServerException::class: // HTTP 5xx
                $exceptionClass = ServerErrorException::class;
                break;
            case GuzzleRedirectsException::class:
                $exceptionClass = TooManyRedirectsException::class;
                break;
            default:
                $exceptionClass = TransferErrorException::class;
                break;
        }

        $self = new $exceptionClass(
            $exception->getMessage(),
            $exception->getCode(),
            $exception
        );

        // Request anhängen; immer verfügbar
        $psrRequest = $exception->getRequest();
        $request = ClientRequest::fromGuzzleRequest($psrRequest);
        $self->request = $request;

        // Response anhängen; NICHT immer verfügbar
        if ($exception->hasResponse()) {
            $psrResponse = $exception->getResponse();
            $response = ServerResponse::fromGuzzleResponse($psrResponse);
            $self->response = $response;
        }

        return $self;
    }

    /**
     * @param ClientRequestInterface  $request
     * @param ServerResponseInterface $response
     *
     * @return TransferErrorExceptionInterface
     */
    public static function fromClientRequest(
        ClientRequestInterface $request,
        ServerResponseInterface $response = null
    ): TransferErrorExceptionInterface {
        $message = sprintf('Error Communicating with Server: %s %s', $request->getMethod(), $request->getUri());

        return new self($message, 0, null, $request, $response);
    }

    /**
     * @return bool
     */
    public function hasResponse(): bool
    {
        return $this->response !== null;
    }

    /**
     * @return ServerResponseInterface|null
     */
    public function getResponse(): ?ServerResponseInterface
    {
        return $this->response;
    }

    /**
     * @return ClientRequestInterface
     */
    public function getRequest(): ClientRequestInterface
    {
        return $this->request;
    }
}
