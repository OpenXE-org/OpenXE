<?php

namespace Xentral\Modules\EPost;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\DocuvitaApi\Exception\ConfigurationMissingException;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'EPostService' => 'onInitEPostService',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @throws ConfigurationMissingException
     *
     * @return EPostService
     */
    public static function onInitEPostService(ContainerInterface $container)
    {
        return new EPostService($container);
    }
}
