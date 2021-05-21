<?php

namespace Xentral\Modules\Country;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\Country\Gateway\CountryGateway;
use Xentral\Modules\Country\Gateway\StateGateway;
use Xentral\Modules\Country\Service\CountryMigrationService;
use Xentral\Modules\Country\Service\CountryService;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'CountryGateway'          => 'onInitCountryGateway',
            'CountryService'          => 'onInitCountryService',
            'CountryMigrationService' => 'onInitCountryMigrationService',
            StateGateway::class       => 'onInitStateGateway',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return CountryGateway
     */
    public static function onInitCountryGateway(ContainerInterface $container)
    {
        return new CountryGateway($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return CountryService
     */
    public static function onInitCountryService(ContainerInterface $container)
    {
        return new CountryService($container->get('CountryGateway'), $container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return CountryMigrationService
     */
    public static function onInitCountryMigrationService(ContainerInterface $container)
    {
        return new CountryMigrationService($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return StateGateway
     */
    public static function onInitStateGateway(ContainerInterface $container): StateGateway
    {
        return new StateGateway($container->get('Database'));
    }
}
