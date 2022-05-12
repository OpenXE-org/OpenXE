<?php

declare(strict_types=1);

namespace Xentral\Modules\Ebay\Module;

use DateTime;
use Xentral\Modules\Ebay\Client\EbayRestApiClient;
use Xentral\Modules\Ebay\Data\AccountCredentialsData;
use Xentral\Modules\Ebay\Data\TokenData;
use Xentral\Modules\Ebay\Gateway\EbayRestApiGateway;
use Xentral\Modules\Ebay\Service\EbayRestApiService;

final class EbayRestApiModule
{
    /** @var EbayRestApiClient $client */
    private $client;
    /** @var EbayRestApiGateway $gateway */
    private $gateway;
    /** @var EbayRestApiService $service */
    private $service;

    /**
     * @param EbayRestApiClient  $client
     * @param EbayRestApiGateway $gateway
     * @param EbayRestApiService $service
     */
    public function __construct(EbayRestApiClient $client, EbayRestApiGateway $gateway, EbayRestApiService $service)
    {
        $this->client = $client;
        $this->gateway = $gateway;
        $this->service = $service;
    }

    /**
     * @param int $shopexportId
     * @param int $categoryId
     *
     * @return array
     */
    public function getCategorySpecificProperties(int $shopexportId, int $categoryId): array
    {
        $siteId = $this->gateway->getSiteId($shopexportId);

        $token = $this->getRestApiApplicationAccessToken($shopexportId);

        return $this->client->getCategorySpecificProperties($siteId, $categoryId, $token);
    }

    /**
     * @param int $shopexportId
     *
     * @return string|null
     */
    public function getRestApiApplicationAccessToken(int $shopexportId): ?string
    {
        $credentials = $this->gateway->getAccountCredentials($shopexportId);
        $ebayResponse = $this->client->getRestApiApplicationAccessTokenFromEbay(
            $credentials
        );
        $this->service->saveRestApiAccessToken($shopexportId, $ebayResponse);

        return $ebayResponse['access_token'];
    }

    public function getOrders(int $shopexportId, DateTime $dateFrom, int $offset, int $limit = null): array
    {
        $token = $this->getRestApiUserAccessToken($shopexportId);

        return $this->client->getOrders($token, $dateFrom, $offset, $limit);
    }

    public function getRestApiUserAccessToken(int $shopexportId): string
    {
        $tokenData = $this->gateway->tryGetRestApiAccessTokenFromDatabase(
            $shopexportId,
            EbayRestApiClient::TOKEN_TYPE_USER
        );
        if ($tokenData === null) {
            //TODO EXCEPTION
            throw new \RuntimeException('Token Request Failure');
        }
        if ($tokenData->isValid()) {
            return $tokenData->getToken();
        }

        $tokenData = $this->renewToken($shopexportId, $tokenData);

        //TODO Exception
        return $tokenData->getToken();
    }

    protected function renewToken(int $shopexportId, TokenData $tokenData): TokenData
    {
        $response = $this->client->renewToken(
            $this->gateway->getAccountCredentials($shopexportId),
            $tokenData
        );

        $tokenData->setToken($response['access_token']);
        $this->service->renewToken($shopexportId, (int)$response['expires_in'], $tokenData);

        return $tokenData;
    }

    public function fetchRestApiUserAccessToken(int $shopexportId, string $requestCode): ?string
    {
        $credentials = $this->gateway->getAccountCredentials($shopexportId);
        $ebayResponse = $this->client->getRestApiUserAccessTokenFromEbay(
            $credentials,
            $requestCode
        );
        $this->service->saveRestApiAccessToken($shopexportId, $ebayResponse);

        if (!isset($ebayResponse['access_token'])) {
            return null;
        }    

        return $ebayResponse['access_token'];
    }

    public function getCompleteApiScope(): array
    {
        return $this->client->getCompleteScope();
    }

    public function getAccountCredentials($shopexportId): AccountCredentialsData
    {
        return $this->gateway->getAccountCredentials($shopexportId);
    }

    public function useRestApiForOrderImport(int $shopexportId): bool
    {
        return $this->gateway->useRestApiForOrderImport($shopexportId);
    }

    public function saveRestOrder(int $shopexportId, DateTime $orderDate, string $orderId, string $orderData): void
    {
        $this->service->saveRestOrder($shopexportId, $orderDate, $orderId, $orderData);
    }

    public function countRestOrdersToImport(int $shopexportId): int
    {
        return $this->gateway->countRestOrdersToImport($shopexportId);
    }

    public function getNextOrderToImport(int $shopexportId): string
    {
        return $this->gateway->getNextOrderToImport($shopexportId);
    }

    public function setRestOrderToProcessed(string $orderId): void
    {
        $this->service->setRestOrderToProcessed($orderId);
    }

    public function deleteRestOrderFromDatabase(string $orderId): void
    {
        $this->service->deleteRestOrderFromDatabase($orderId);
    }

    public function existsRestOrderInDatabase(string $orderId): bool
    {
        return $this->gateway->existsRestOrderInDatabase($orderId);
    }
}
