<?php

declare(strict_types=1);

namespace Xentral\Modules\PartialDelivery;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\PartialDelivery\Service\PartialDeliveryService;

class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'PartialDeliveryService' => 'onInitPartialDeliveryService',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PartialDeliveryService
     */
    public static function onInitPartialDeliveryService(ContainerInterface $container): PartialDeliveryService
    {
        return new PartialDeliveryService($container->get('Database'));
    }
}
