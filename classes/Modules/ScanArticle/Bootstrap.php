<?php

namespace Xentral\Modules\ScanArticle;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\ScanArticle\Service\ScanArticleService;
use Xentral\Modules\ScanArticle\Wrapper\PriceWrapper;
use Xentral\Modules\ScanArticle\Wrapper\SavePositionWrapper;

final class Bootstrap
{

    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'ScanArticleService'  => 'onInitScanArticleService'
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ScanArticleService
     */
    public static function onInitScanArticleService(ContainerInterface $container)
    {
        return new ScanArticleService(
            $container->get('ArticleGateway'),
            $container->get('Session'),
            self::onInitPriceWrapper($container),
            self::onInitSavePositionWrapper($container)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PriceWrapper
     */
    public static function onInitPriceWrapper(ContainerInterface $container)
    {
        /** @var \ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new PriceWrapper(
            $app->erp,
            $container->get('Database')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SavePositionWrapper
     */
    public static function onInitSavePositionWrapper(ContainerInterface $container)
    {
        /** @var \ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new SavePositionWrapper(
            $app->erp,
            $container->get('Database')
        );
    }
}
