<?php

namespace Xentral\Modules\DownloadSpooler;

use Xentral\Core\DependencyInjection\ContainerInterface;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'DownloadSpoolerService' => 'onInitDownloadSpoolerService',
            'DownloadSpoolerGateway' => 'onInitDownloadSpoolerGateway',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return DownloadSpoolerService
     */
    public static function onInitDownloadSpoolerService(ContainerInterface $container)
    {
        /** @var \ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new DownloadSpoolerService($container->get('Database'), $app->erp);
    }

    /**
     * @param ContainerInterface $container
     *
     * @return DownloadSpoolerGateway
     */
    public static function onInitDownloadSpoolerGateway(ContainerInterface $container)
    {
        return new DownloadSpoolerGateway($container->get('Database'));
    }
}
