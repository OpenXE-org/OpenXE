<?php

namespace Xentral\Modules\CalDav;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\CalDav\SabreDavBackend\WawisionAuthBackend;
use Xentral\Modules\CalDav\SabreDavBackend\WawisionCalendarBackend;
use Xentral\Modules\CalDav\SabreDavBackend\WawisionPrincipalBackend;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'WawisionAuthBackend'      => 'onInitAuthBackend',
            'WawisionCalendarBackend'  => 'onInitCalendarBackend',
            'WawisionPrincipalBackend' => 'onInitPrincipalBackend',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return WawisionAuthBackend
     */
    public static function onInitAuthBackend(ContainerInterface $container)
    {
        return new WawisionAuthBackend($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return WawisionCalendarBackend
     */
    public static function onInitCalendarBackend(ContainerInterface $container)
    {
        return new WawisionCalendarBackend($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return WawisionPrincipalBackend
     */
    public static function onInitPrincipalBackend(ContainerInterface $container)
    {
        return new WawisionPrincipalBackend($container->get('Database'));
    }
}
