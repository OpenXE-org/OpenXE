<?php

namespace Xentral\Modules\Backup;

use ApplicationCore;
use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\Backup\Scheduler\BackupScheduleTask;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'BackupGateway'                    => 'onInitBackupGateway',
            'BackupService'                    => 'onInitBackupService',
            'BackupSystemConfigurationService' => 'onInitBackupSystemConfigurationService',
            'BackupProcessStarterService'      => 'onInitBackupProcessStarterService',
            'BackupNotificationService'        => 'onInitBackupNotificationService',
            'BackupScheduleTask'               => 'onInitBackupTask',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return BackupGateway
     */
    public static function onInitBackupGateway(ContainerInterface $container)
    {
        return new BackupGateway($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return BackupService
     */
    public static function onInitBackupService(ContainerInterface $container)
    {
        return new BackupService(
            $container->get('BackupGateway'),
            $container->get('DatabaseBackup'),
            $container->get('FileBackup'),
            $container->get('BackupProcessStarterService'),
            $container->get('BackupSystemConfigurationService'),
            $container->get('Database'),
            $container->get('BackupLog')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return BackupSystemConfigurationService
     */
    public static function onInitBackupSystemConfigurationService(ContainerInterface $container)
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new BackupSystemConfigurationService($app->erp);
    }

    public static function onInitBackupProcessStarterService(ContainerInterface $container)
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new BackupProcessStarterService($app->erp);
    }

    public static function onInitBackupNotificationService(ContainerInterface $container)
    {
        return new BackupNotificationService(
            $container->get('BackupSystemConfigurationService'),
            $container->get('NotificationService')
        );
    }

    public function onInitBackupTask(ContainerInterface $container)
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new BackupScheduleTask(
            $container->get('Database'),
            $container->get('BackupSystemConfigurationService'),
            $container->get('BackupNotificationService'),
            $app,
            $container->get('BackupService')
        );
    }
}
