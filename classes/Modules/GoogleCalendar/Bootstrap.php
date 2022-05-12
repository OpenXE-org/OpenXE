<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleCalendar;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\GoogleCalendar\Client\GoogleCalendarClientFactory;
use Xentral\Modules\GoogleCalendar\Service\GoogleCalendarSynchronizer;
use Xentral\Modules\GoogleCalendar\Service\GoogleEventConverter;
use Xentral\Modules\GoogleCalendar\Service\GoogleSyncGateway;
use Xentral\Modules\GoogleCalendar\Service\GoogleSyncService;
use Xentral\Modules\GoogleCalendar\Wrapper\UserAddressGatewayWrapper;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'GoogleCalendarClientFactory' => 'onInitGoogleCalendarClientFactory',
            'GoogleSyncGateway'           => 'onInitGoogleSyncEntryGateway',
            'GoogleSyncService'           => 'onInitGoogleSyncEntryService',
            'GoogleEventConverter'        => 'onInitGoogleEventConverter',
            'GoogleCalendarSynchronizer'  => 'onInitGoogleCalendarSynchronizer',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return GoogleCalendarClientFactory
     */
    public static function onInitGoogleCalendarClientFactory(ContainerInterface $container): GoogleCalendarClientFactory
    {
        return new GoogleCalendarClientFactory(
            $container->get('GoogleApiClientFactory'),
            $container->get('GoogleAccountGateway')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return GoogleSyncGateway
     */
    public static function onInitGoogleSyncEntryGateway(ContainerInterface $container): GoogleSyncGateway
    {
        return new GoogleSyncGateway(
            $container->get('Database')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return GoogleSyncService
     */
    public static function onInitGoogleSyncEntryService(ContainerInterface $container): GoogleSyncService
    {
        return new GoogleSyncService(
            $container->get('Database'),
            $container->get('GoogleSyncGateway')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return GoogleEventConverter
     */
    public static function onInitGoogleEventConverter(ContainerInterface $container): GoogleEventConverter
    {
        return new GoogleEventConverter(
            self::onInitUserAddressGatewayWrapper($container)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return GoogleCalendarSynchronizer
     */
    public static function onInitGoogleCalendarSynchronizer(ContainerInterface $container): GoogleCalendarSynchronizer
    {
        return new GoogleCalendarSynchronizer(
            $container->get('GoogleSyncGateway'),
            $container->get('GoogleSyncService'),
            $container->get('CalendarService'),
            $container->get('GoogleEventConverter'),
            self::onInitUserAddressGatewayWrapper($container),
            $container->get('UserConfigService')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return UserAddressGatewayWrapper
     */
    private static function onInitUserAddressGatewayWrapper(ContainerInterface $container): UserAddressGatewayWrapper
    {
        return new UserAddressGatewayWrapper($container->get('Database'));
    }
}
