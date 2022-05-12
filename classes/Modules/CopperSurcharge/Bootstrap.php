<?php

namespace Xentral\Modules\CopperSurcharge;

use ApplicationCore;
use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\CopperSurcharge\Service\DocumentGateway;
use Xentral\Modules\CopperSurcharge\Service\PurchasePriceGateway;
use Xentral\Modules\CopperSurcharge\Service\DocumentService;
use Xentral\Modules\CopperSurcharge\Service\RawMaterialGateway;
use Xentral\Modules\CopperSurcharge\Wrapper\CompanyDataWrapper;
use Xentral\Modules\CopperSurcharge\Wrapper\DocumentPositionWrapper;


final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'CopperSurchargeCalculatorFactory' => 'onInitCopperSurchargeCalculatorFactory',
            'CopperSurchargeService'           => 'onInitCopperSurchargeService',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return CopperSurchargeCalculatorFactory
     */
    public static function onInitCopperSurchargeCalculatorFactory(ContainerInterface $container
    ): CopperSurchargeCalculatorFactory {
        return new CopperSurchargeCalculatorFactory(
            self::onInitPurchasePriceGateway($container),
            self::onInitRawMaterialGateway($container),
            self::onInitDocumentPositionWrapper($container),
            self::onInitDocumentService($container),
            self::onInitDocumentGateway($container)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return CopperSurchargeService
     */
    public static function onInitCopperSurchargeService(ContainerInterface $container
    ): CopperSurchargeService {
        return new CopperSurchargeService(
            $container->get('SystemConfigModule'),
            self::onInitCompanyDataWrapper($container)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PurchasePriceGateway
     */
    private static function onInitPurchasePriceGateway(ContainerInterface $container
    ): PurchasePriceGateway {
        return new PurchasePriceGateway(
            $container->get('Database')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return RawMaterialGateway
     */
    private static function onInitRawMaterialGateway(ContainerInterface $container
    ): RawMaterialGateway {
        return new RawMaterialGateway(
            $container->get('Database')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return DocumentPositionWrapper
     */
    private static function onInitDocumentPositionWrapper(ContainerInterface $container
    ): DocumentPositionWrapper {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new DocumentPositionWrapper($app->erp, $container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return CompanyDataWrapper
     */
    private static function onInitCompanyDataWrapper(ContainerInterface $container
    ): CompanyDataWrapper {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new CompanyDataWrapper($app->erp);
    }

    /**
     * @param ContainerInterface $container
     *
     * @return DocumentService
     */
    private static function onInitDocumentService(ContainerInterface $container): DocumentService
    {
        return new DocumentService($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return DocumentGateway
     */
    private static function onInitDocumentGateway(ContainerInterface $container): DocumentGateway
    {
        return new DocumentGateway($container->get('Database'));
    }
}
