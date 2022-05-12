<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm;

use ApplicationCore;
use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\Datanorm\Service\DatanormEnricher;
use Xentral\Modules\Datanorm\Service\DatanormIntermediateGateway;
use Xentral\Modules\Datanorm\Service\DatanormIntermediateService;
use Xentral\Modules\Datanorm\Service\DatanormConverter;
use Xentral\Modules\Datanorm\Service\ArticleService;
use Xentral\Modules\Datanorm\Handler\DatanormReaderVersionFourHandler;
use Xentral\Modules\Datanorm\Handler\DatanormReaderVersionFiveHandler;
use Xentral\Modules\Datanorm\Wrapper\AddressWrapper;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'DatanormImporter'      => 'onInitDatanormImporter',
            'DatanormReaderFactory' => 'onInitDatanormReaderFactory',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return DatanormImporter
     */
    public static function onInitDatanormImporter(ContainerInterface $container): DatanormImporter
    {
        return new DatanormImporter(
            self::onInitDatanormIntermediateService($container),
            self::onInitDatanormIntermediateGateway($container),
            self::onInitDatanormConverter(),
            self::onInitArticleService($container),
            $container->get('SellingPriceService'),
            $container->get('PurchasePriceService'),
            self::onInitAdressWrapper($container),
            self::onInitDatanormEnricher($container)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return DatanormReaderFactory
     */
    public static function onInitDatanormReaderFactory(ContainerInterface $container): DatanormReaderFactory
    {
        $handlers[] = new DatanormReaderVersionFiveHandler();
        $handlers[] = new DatanormReaderVersionFourHandler();

        return new DatanormReaderFactory(
            self::onInitDatanormIntermediateService($container),
            $handlers,
            $container->get('FilesystemFactory')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return DatanormIntermediateService
     */
    public static function onInitDatanormIntermediateService(ContainerInterface $container): DatanormIntermediateService
    {
        return new DatanormIntermediateService($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return DatanormIntermediateGateway
     */
    public static function onInitDatanormIntermediateGateway(ContainerInterface $container): DatanormIntermediateGateway
    {
        return new DatanormIntermediateGateway($container->get('Database'));
    }

    /**
     *
     * @return DatanormConverter
     */
    public static function onInitDatanormConverter(): DatanormConverter
    {
        return new DatanormConverter();
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ArticleService
     */
    public static function onInitArticleService(ContainerInterface $container): ArticleService
    {
        return new ArticleService($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return AddressWrapper
     */
    private static function onInitAdressWrapper(ContainerInterface $container): AddressWrapper
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new AddressWrapper(
            $container->get('Database'),
            $app->erp
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return DatanormEnricher
     */
    private static function onInitDatanormEnricher(ContainerInterface $container): DatanormEnricher
    {
        return new DatanormEnricher(
            self::onInitDatanormIntermediateGateway($container)
        );
    }
}
