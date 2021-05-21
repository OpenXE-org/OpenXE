<?php

namespace Xentral\Modules\Klarna;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use Xentral\Modules\Klarna\Exceptions\OrderNotFoundException;

class ApiClient
{
    /** @var string */
    private $user;
    /** @var string */
    private $password;
    /** @var ClientInterface */
    private $client;

    public function __construct(string $user, string $password, bool $onlyTest = false)
    {
        $this->user = $user;
        $this->password = $password;
        $this->client = new Client(
            [
                'base_uri' => $onlyTest ? 'https://api.playground.klarna.com/' : 'https://api.klarna.com',
                'auth'     => [$user, $password],
            ]
        );
    }

    public function testCredentials(): bool
    {
        try {
            // just a random UUID form Wikipedia. It is not available in Klarna
            $this->client->request('GET', '/ordermanagement/v1/orders/550e8400-e29b-11d4-a716-446655440000');
        } catch (BadResponseException $exception) {
            // If status code is not 404 it would be unauthenticated 403
            return $exception->getResponse()->getStatusCode() === 404;
        }

        return true;
    }

    public function createCapture(string $orderId, int $amountInCents): string
    {
        try {
            $response = $this->client->request(
                'POST',
                "/ordermanagement/v1/orders/{$orderId}/captures",
                ['json' => ['captured_amount' => $amountInCents]]
            );
        } catch (BadResponseException $exception) {
            if ($exception->getResponse()->getStatusCode() === 404) {
                throw new OrderNotFoundException("Order not found: {$orderId}");
            }
            throw $exception;
        }

        return $response->getHeaderLine('Capture-Id');
    }

    public function getOrder(string $orderId): array
    {
        try {
            $response = $this->client->request('GET', "/ordermanagement/v1/orders/{$orderId}");
        } catch (BadResponseException $exception) {
            if ($exception->getResponse()->getStatusCode() === 404) {
                throw new OrderNotFoundException("Order not found: {$orderId}");
            }
            throw $exception;
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    public function acknowledgeOrder(string $orderId): void
    {
        try {
            $this->client->request('POST', "/ordermanagement/v1/orders/{$orderId}/acknowledge");
        } catch (BadResponseException $exception) {
            if ($exception->getResponse()->getStatusCode() === 404) {
                throw new OrderNotFoundException("Order not found: {$orderId}");
            }
            throw $exception;
        }
    }
}
