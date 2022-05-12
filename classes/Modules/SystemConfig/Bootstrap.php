<?php

declare(strict_types=1);

namespace Xentral\Modules\SystemConfig;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\SystemConfig\Gateway\SystemConfigGateway;
use Xentral\Modules\SystemConfig\Helper\SystemConfigHelper;
use Xentral\Modules\SystemConfig\Service\SystemConfigService;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'SystemConfigModule' => 'onInitSystemConfigModule',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SystemConfigService
     */
    public static function onInitSystemConfigModule(ContainerInterface $container): SystemConfigModule
    {
        return new SystemConfigModule(
            self::onInitSystemConfigService($container),
            self::onInitSystemConfigGateway($container)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SystemConfigService
     */
    private static function onInitSystemConfigService(ContainerInterface $container): SystemConfigService
    {
        return new SystemConfigService(
            self::onInitSystemConfigGateway($container),
            $container->get('Database'),
            self::onInitSystemConfigHelper()
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SystemConfigGateway
     */
    private static function onInitSystemConfigGateway(ContainerInterface $container): SystemConfigGateway
    {
        return new SystemConfigGateway(
            $container->get('Database'),
            self::onInitSystemConfigHelper()
        );
    }

    /**
     * @return SystemConfigHelper
     */
    private static function onInitSystemConfigHelper(): SystemConfigHelper
    {
        return new SystemConfigHelper();
    }
}
