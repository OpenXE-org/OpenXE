<?php

namespace Xentral\Modules\DemoExporter;

use ApplicationCore;
use Xentral\Core\DependencyInjection\ContainerInterface;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'DemoExporterService' => 'onInitDemoExporterService',
            'DemoExporterGateway' => 'onInitDemoExporterGateway',
        ];
    }

    public static function onInitDemoExporterService(ContainerInterface $container)
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new DemoExporterService(
            new DemoExporterDateiService($app),
            new DemoExporterCleanerService($app),
            $container->get('Database'),
            $container->get('BackupSystemConfigurationService'),
            $container->get('BackupService'),
            $container->get('DemoExporterGateway'),
            $container->get('BackupLog')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return DemoExporterGateway
     */
    public static function onInitDemoExporterGateway(ContainerInterface $container)
    {
        return new DemoExporterGateway($container->get('Database'));
    }

}
