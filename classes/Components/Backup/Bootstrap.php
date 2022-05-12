<?php

namespace Xentral\Components\Backup;

use ApplicationCore;
use Xentral\Components\Backup\Exception\BackupException;
use Xentral\Components\Backup\Adapter\ExecAdapter;
use Xentral\Components\Backup\Logger\BackupLog;
use Xentral\Core\DependencyInjection\ContainerInterface;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'DatabaseBackup' => 'onInitDatabaseBackup',
            'FileBackup'     => 'onInitFileBackup',
            'BackupLog'      => 'onInitBackupLogger',
        ];
    }

    /**
     *
     * @param ContainerInterface $container
     *
     * @return DatabaseBackup
     */
    public static function onInitDatabaseBackup(ContainerInterface $container)
    {
        //@codeCoverageIgnoreStart
        if (!function_exists('exec')) {
            throw new BackupException(sprintf('function "%s" is missing!', 'exec'));
        }
        //@codeCoverageIgnoreEnd
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new DatabaseBackup(new ExecAdapter(), $app->erp->getTMP());
    }

    /**
     * @param ContainerInterface $container
     *
     * @return FileBackup
     */
    public static function onInitFileBackup(ContainerInterface $container)
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new FileBackup($container->get('BackupLog'), $app->erp->getTMP());
    }

    public static function onInitBackupLogger(ContainerInterface $container)
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        /** @var string $path */
        $path = $app->erp->GetRootPath() . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR;

        return new BackupLog($path, 'status.txt');
    }
}
