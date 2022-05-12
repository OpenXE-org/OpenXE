<?php

namespace Xentral\Modules\AmazonVendorDF;

use Aws\Credentials\Credentials;
use Aws\Signature\SignatureV4;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Xentral\Modules\AmazonVendorDF\Data\Token;
use Xentral\Modules\AmazonVendorDF\Service\InventoryService;
use Xentral\Modules\AmazonVendorDF\Service\InvoiceService;
use Xentral\Modules\AmazonVendorDF\Service\PurchaseOrderService;
use Xentral\Modules\AmazonVendorDF\Service\ShippingService;
use Xentral\Modules\AmazonVendorDF\Service\TokenService;
use Xentral\Modules\AmazonVendorDF\Service\TransactionService;

class ServiceFactory
{
    const API_BASE_URL = 'https://sellingpartnerapi-eu.amazon.com';

    /** @var TokenService */
    private $tokenService;
    /** @var Token */
    private $token;
    /** @var string */
    private $refreshToken;
    /** @var string */
    private $clientId;
    /** @var string */
    private $clientSecret;
    /** @var SignatureV4 */
    private $signature;
    /** @var Credentials */
    private $credentials;
    /** @var ClientInterface */
    private $authenticatedClient;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        string $refreshToken,
        string $clientId,
        string $clientSecret,
        string $awsIamKey,
        string $awsIamSecret,
        LoggerInterface $logger
    ) {
        $this->tokenService = new TokenService(new Client());
        $this->refreshToken = $refreshToken;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->signature = new SignatureV4('execute-api', 'eu-west-1');
        $this->credentials = new Credentials($awsIamKey, $awsIamSecret);
        $this->logger = $logger;
    }

    public function getShippingService(): ShippingService
    {
        return new ShippingService($this->getAuthenticatedClient());
    }

    public function getInvoiceService(): InvoiceService
    {
        return new InvoiceService($this->getAuthenticatedClient());
    }

    public function getInventoryService(): InventoryService
    {
        return new InventoryService($this->getAuthenticatedClient());
    }

    public function getPurchaseOrderService(): PurchaseOrderService
    {
        return new PurchaseOrderService($this->getAuthenticatedClient());
    }

    public function getTransactionService(): TransactionService
    {
        return new TransactionService($this->getAuthenticatedClient());
    }

    private function getAuthenticatedClient(): ClientInterface
    {
        if (!$this->authenticatedClient || $this->getToken()->isExpired()) {
            $stack = HandlerStack::create();
            $stack->push($this->getSignatureMiddleware());
            $stack->push($this->getLoggingMiddleWare());

            $this->authenticatedClient = new Client(
                [
                    'handler'  => $stack,
                    'base_uri' => self::API_BASE_URL,
                    'headers'  => [
                        'x-amz-access-token' => $this->getToken()->getAccessToken(),
                    ],
                ]
            );
        }

        return $this->authenticatedClient;
    }

    private function getSignatureMiddleware()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                return $handler($this->signature->signRequest($request, $this->credentials), $options);
            };
        };
    }

    private function getLoggingMiddleWare()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $promise = $handler($request, $options);

                return $promise->then(
                    function (ResponseInterface $response) use ($request) {
                        $this->logRequestAndResponse($request, $response, LogLevel::DEBUG);

                        return $response;
                    },
                    function (ResponseInterface $response) use ($request) {
                        $this->logRequestAndResponse($request, $response, LogLevel::ERROR);

                        return $response;
                    }
                );
            };
        };
    }

    private function logRequestAndResponse(RequestInterface $request, ResponseInterface $response, string $level)
    {
        $request->getBody()->rewind();

        $this->logger->log(
            $level,
            'Amazon Vendor DF API request',
            [
                'request'  => [
                    'uri'    => (string)$request->getUri(),
                    'method' => $request->getMethod(),
                    'body'   => json_decode($request->getBody()->getContents(), true),
                ],
                'response' => [
                    'status_code' => $response->getStatusCode(),
                    'headers'     => $response->getHeaders(),
                    'body'        => json_decode($response->getBody()->getContents(), true),
                ],
            ]

        );

        $response->getBody()->rewind();
    }

    private function getToken()
    {
        if (!$this->token) {
            $this->token = $this->tokenService->requestToken($this->refreshToken, $this->clientId, $this->clientSecret);
        }

        return $this->token;
    }
}
