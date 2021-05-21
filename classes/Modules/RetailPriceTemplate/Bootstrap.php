<?php

namespace Xentral\Modules\RetailPriceTemplate;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\RetailPriceTemplate\Service\RetailPriceTemplateService;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'RetailPriceTemplateService' => 'onInitRetailPriceTemplateService',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return RetailPriceTemplateService
     */
    public static function onInitRetailPriceTemplateService(ContainerInterface $container)
    {
        return new RetailPriceTemplateService($container->get('Database'));
    }
}
