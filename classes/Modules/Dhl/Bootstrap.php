<?php

namespace Xentral\Modules\Dhl;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\Dhl\Api\DhlApi;
use Xentral\Modules\Dhl\Factory\DhlApiFactory;
use Xentral\Modules\DocuvitaApi\Exception\ConfigurationMissingException;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'DhlApiFactory' => 'onInitDhlApiFactory',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return DhlApiFactory
     */
    public static function onInitDhlApiFactory(ContainerInterface $container)
    {
        return new DhlApiFactory();
    }
}
