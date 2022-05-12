<?php

namespace Xentral\Modules\ShippingTaxSplit;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\ShippingTaxSplit\Gateway\ShippingTaxSplitGateway;
use Xentral\Modules\ShippingTaxSplit\Service\ShippingTaxSplitService;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'ShippingTaxSplitService' => 'onInitShippingTaxSplitService',
            'ShippingTaxSplitGateway' => 'onInitShippingTaxSplitGateway',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ShippingTaxSplitService
     */
    public static function onInitShippingTaxSplitService(ContainerInterface $container)
    {
        return new ShippingTaxSplitService(
            $container->get('Database'),
            $container->get('ShippingTaxSplitGateway'),
            $container->get('LegacyApplication')->erp
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ShippingTaxSplitGateway
     */
    public static function onInitShippingTaxSplitGateway(ContainerInterface $container)
    {
        return new ShippingTaxSplitGateway($container->get('Database'));
    }
}
