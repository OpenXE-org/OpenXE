<?php

declare(strict_types=1);

namespace Xentral\Modules\Ebay;

use ApplicationCore;
use GuzzleHttp\Client;
use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\Ebay\Client\EbayRestApiClient;
use Xentral\Modules\Ebay\Gateway\EbayListingGateway;
use Xentral\Modules\Ebay\Gateway\EbayRestApiGateway;
use Xentral\Modules\Ebay\Module\EbayRestApiModule;
use Xentral\Modules\Ebay\Service\EbayListingService;
use Xentral\Modules\Ebay\Service\EbayListingXmlSerializer;
use Xentral\Modules\Ebay\Service\EbayRestApiService;
use Xentral\Modules\Ebay\Service\EbayStockLoggingService;
use Xentral\Modules\Ebay\Wrapper\StockCalculationWrapper;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'EbayListingGateway'      => 'onInitEbayListingGateway',
            'EbayListingService'      => 'onInitEbayListingService',
            'EbayRestApiModule'       => 'onInitEbayRestApiModule',
            'EbayStockLoggingService' => 'onInitEbayStockLoggingService',
            'EbayRestApiGateway'      => 'onInitEbayRestApiGateway',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return EbayListingService
     */
    public static function onInitEbayListingService(ContainerInterface $container): EbayListingService
    {
        return new EbayListingService(
            $container->get('EbayListingGateway'),
            $container->get('Database'),
            new EbayListingXmlSerializer(),
            self::onInitStockCalculationWrapper($container)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return StockCalculationWrapper
     */
    private static function onInitStockCalculationWrapper(ContainerInterface $container): StockCalculationWrapper
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new StockCalculationWrapper($app->erp, $container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return EbayStockLoggingService
     */
    public static function onInitEbayStockLoggingService(ContainerInterface $container): EbayStockLoggingService
    {
        return new EbayStockLoggingService($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return EbayListingGateway
     */
    public static function onInitEbayListingGateway(ContainerInterface $container): EbayListingGateway
    {
        return new EbayListingGateway($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return EbayRestApiModule
     */
    public static function onInitEbayRestApiModule(ContainerInterface $container): EbayRestApiModule
    {
        return new EbayRestApiModule(
            self::onInitEbayRestApiClient(),
            self::onInitEbayRestApiGateway($container),
            self::onInitEbayRestApiService($container)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return EbayRestApiGateway
     */
    public static function onInitEbayRestApiGateway(ContainerInterface $container): EbayRestApiGateway
    {
        return new EbayRestApiGateway($container->get('Database'));
    }

    /**
     * @return EbayRestApiClient
     */
    private static function onInitEbayRestApiClient(): EbayRestApiClient
    {
        return new EbayRestApiClient(new Client());
    }

    /**
     * @param ContainerInterface $container
     *
     * @return EbayRestApiService
     */
    private static function onInitEbayRestApiService(ContainerInterface $container): EbayRestApiService
    {
        return new EbayRestApiService($container->get('Database'));
    }
}
