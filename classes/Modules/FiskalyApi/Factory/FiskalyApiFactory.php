<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Factory;

use Exception;
use Xentral\Modules\FiskalyApi\Data\Organisation;
use Xentral\Modules\FiskalyApi\Service\FiskalyApi;
use Xentral\Modules\FiskalyApi\Service\FiskalyConfig;
use Xentral\Modules\FiskalyApi\Service\FiskalyDSFinVKApi;
use Xentral\Modules\FiskalyApi\Service\FiskalyEReceiptApi;
use Xentral\Modules\FiskalyApi\Service\FiskalyKassenSichVApi;
use Xentral\Modules\FiskalyApi\Service\FiskalyManagementApi;
use Xentral\Modules\SystemConfig\SystemConfigModule;

class FiskalyApiFactory
{
    /** @var FiskalyConfig */
    private $fiskalyConfig;

    /**
     * FiskalyApiFactory constructor.
     *
     * @param FiskalyConfig $fiskalyConfig
     */
    public function __construct(FiskalyConfig $fiskalyConfig)
    {
        $this->fiskalyConfig = $fiskalyConfig;
    }

    /**
     * @return int
     */
    public function getMaxTssIds(): int
    {
        return $this->fiskalyConfig->getMaxTss();
    }

    /**
     * @return array
     */
    public function getOrganizations(): array
    {
        return array_map(
            static function ($organization){
                return Organisation::fromDbState($organization);
            },
            $this->fiskalyConfig->getOrganisations()
        );
    }

    /**
     * @param string $organization
     *
     * @throws Exception
     * @return FiskalyKassenSichVApi
     */
    public function createFiskalyKassenSichVApiFromSystemSettings(string $organization): FiskalyKassenSichVApi
    {
        return $this->createFiskalyKassenSichVApi(
            (string)$this->fiskalyConfig->getActiveSmaEndpoint($organization),
            (string)$this->fiskalyConfig->getApiKey($organization),
            (string)$this->fiskalyConfig->getApiSecret($organization)
        );
    }

    /**
     * @param string $organization
     *
     * @throws Exception
     * @return FiskalyManagementApi
     */
    public function createFiskalyManagementApiFromSystemSettings(string $organization): FiskalyManagementApi
    {
        return $this->createFiskalyManagementApi(
            $this->fiskalyConfig->getActiveSmaEndpoint($organization),
            $this->fiskalyConfig->getApiKey($organization),
            $this->fiskalyConfig->getApiSecret($organization)
        );
    }

    /**
     * @param string $organization
     *
     * @throws Exception
     * @return FiskalyDSFinVKApi
     */
    public function createFiskalyDSFinVkApiFromSystemSettings(string $organization): FiskalyDSFinVKApi
    {
        return $this->createFiskalyDSFinVkApi(
            $this->fiskalyConfig->getActiveSmaEndpoint($organization),
            $this->fiskalyConfig->getApiKey($organization),
            $this->fiskalyConfig->getApiSecret($organization)
        );
    }

    /**
     * @param string $organization
     *
     * @throws Exception
     * @return FiskalyEReceiptApi
     */
    public function createFiskalyEReceiptApiFromSystemSettings(string $organization): FiskalyEReceiptApi
    {
        return $this->createFiskalyEReceiptApi(
            $this->fiskalyConfig->getActiveSmaEndpoint($organization),
            $this->fiskalyConfig->getApiKey($organization),
            $this->fiskalyConfig->getApiSecret($organization)
        );
    }


    /**
     * @param string $smaEndpoint
     * @param string $apiKey
     * @param string $apiSecret
     *
     * @throws Exception
     *
     * @return FiskalyKassenSichVApi
     */
    public function createFiskalyKassenSichVApi(
        string $smaEndpoint,
        string $apiKey,
        string $apiSecret
    ): FiskalyKassenSichVApi {
        return new FiskalyKassenSichVApi(
            $smaEndpoint,
            $apiKey,
            $apiSecret
        );
    }

    /**
     * @param string $smaEndpoint
     * @param string $apiKey
     * @param string $apiSecret
     *
     * @throws Exception
     *
     * @return FiskalyManagementApi
     */
    public function createFiskalyManagementApi(
        string $smaEndpoint,
        string $apiKey,
        string $apiSecret
    ): FiskalyManagementApi {
        return new FiskalyManagementApi(
            $smaEndpoint,
            $apiKey,
            $apiSecret
        );
    }

    /**
     * @param string $smaEndpoint
     * @param string $apiKey
     * @param string $apiSecret
     *
     * @throws Exception
     *
     * @return FiskalyDSFinVKApi
     */
    public function createFiskalyDSFinVkApi(string $smaEndpoint, string $apiKey, string $apiSecret): FiskalyDSFinVKApi
    {
        return new FiskalyDSFinVKApi(
            $smaEndpoint,
            $apiKey,
            $apiSecret
        );
    }

    /**
     * @param string $smaEndpoint
     * @param string $apiKey
     * @param string $apiSecret
     *
     * @throws Exception
     * @return FiskalyEReceiptApi
     */
    public function createFiskalyEReceiptApi(string $smaEndpoint, string $apiKey, string $apiSecret): FiskalyEReceiptApi
    {
        return new FiskalyEReceiptApi(
            $smaEndpoint,
            $apiKey,
            $apiSecret
        );
    }
}
