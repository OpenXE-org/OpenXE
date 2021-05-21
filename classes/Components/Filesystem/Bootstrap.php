<?php

namespace Xentral\Components\Filesystem;

use Xentral\Core\DependencyInjection\ContainerInterface;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'FilesystemFactory' => 'onInitFilesystemFactory',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return FilesystemFactory
     */
    public static function onInitFilesystemFactory(ContainerInterface $container)
    {
        return new FilesystemFactory($container->get('Database'));
    }
}
