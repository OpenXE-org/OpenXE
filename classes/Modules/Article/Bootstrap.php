<?php

namespace Xentral\Modules\Article;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\Article\Service\SellingPriceService;
use Xentral\Modules\Article\Gateway\ArticleGateway;
use Xentral\Modules\Article\Service\CurrencyConversionService;
use Xentral\Modules\Article\Service\PurchasePriceService;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'ArticleGateway'            => 'onInitArticleGateway',
            'PurchasePriceService'      => 'onInitPurchasePriceService',
            'SellingPriceService'       => 'onInitSellingPriceService',
            'CurrencyConversionService' => 'onInitCurrencyConversionService',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ArticleGateway
     */
    public static function onInitArticleGateway(ContainerInterface $container)
    {
        return new ArticleGateway($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PurchasePriceService
     */
    public static function onInitPurchasePriceService(ContainerInterface $container)
    {
        return new PurchasePriceService($container->get('ArticleGateway'), $container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SellingPriceService
     */
    public static function onInitSellingPriceService(ContainerInterface $container)
    {
        return new SellingPriceService($container->get('Database'), $container->get('CurrencyConversionService'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return CurrencyConversionService
     */
    public static function onInitCurrencyConversionService(ContainerInterface $container)
    {
        /** @var \ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new CurrencyConversionService($app->erp);
    }
}
