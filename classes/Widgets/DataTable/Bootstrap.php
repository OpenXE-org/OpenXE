<?php

namespace Xentral\Widgets\DataTable;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Widgets\DataTable\Service\DataTableRequestHandler;
use Xentral\Widgets\DataTable\Service\DataTableService;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'DataTableService'        => 'onInitDataTableService',
            'DataTableRequestHandler' => 'onInitDataTableRequestHandler',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return DataTableService
     */
    public static function onInitDataTableService(ContainerInterface $container)
    {
        $factory = new DataTableFactory($container);

        return $factory->createDataTableService();
    }

    /**
     * @param ContainerInterface $container
     *
     * @return DataTableRequestHandler
     */
    public static function onInitDataTableRequestHandler(ContainerInterface $container)
    {
        $factory = new DataTableFactory($container);

        return $factory->createDataTableRequestHandler();
    }
}
