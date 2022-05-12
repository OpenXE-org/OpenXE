<?php

declare(strict_types=1);

namespace Xentral\Components\EnvironmentConfig;

use SplFileInfo;
use Xentral\Core\DependencyInjection\ContainerInterface;
use Config;
use License;
use Xentral\Modules\Api\LegacyBridge\LegacyApplication;

class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'EnvironmentConfig' => 'onInitEnvironmentConfig',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return EnvironmentConfig
     */
    public static function onInitEnvironmentConfig(ContainerInterface $container): EnvironmentConfig
    {
        $provider = self::onInitEnvironmentConfigProvider($container);

        return $provider->createEnvironmentConfig();
    }

    /**
     * @param ContainerInterface $container
     *
     * @return EnvironmentConfigProvider
     */
    private static function onInitEnvironmentConfigProvider(ContainerInterface $container): EnvironmentConfigProvider
    {
        /** @var LegacyApplication $app */
        $app = $container->get('LegacyApplication');

        if (!class_exists('License')) {
            $test = dirname(__DIR__, 3) . '/phpwf/plugins/class.license.php';
            $file = new SplFileInfo($test);
            if ($file->isFile()) {
                include $test;
            }
        }

        return new EnvironmentConfigProvider(new License(), $app->Conf);
    }
}
