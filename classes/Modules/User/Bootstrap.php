<?php

namespace Xentral\Modules\User;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\User\Service\UserConfigService;
use Xentral\Modules\User\Service\UserPermissionService;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'UserConfigService'     => 'onInitUserConfigService',
            'UserPermissionService' => 'onInitUserPermissionService',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return UserConfigService
     */
    public static function onInitUserConfigService(ContainerInterface $container)
    {
        return new UserConfigService($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return UserPermissionService
     */
    public static function onInitUserPermissionService(ContainerInterface $container)
    {
        return new UserPermissionService($container->get('Database'));
    }
}
