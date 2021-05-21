<?php

namespace Xentral\Modules\Calendar;

use Xentral\Core\DependencyInjection\ContainerInterface;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'CalendarService'  => 'onInitCalendarService'
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return CalendarService
     */
    public static function onInitCalendarService(ContainerInterface $container)
    {
        return new CalendarService($container->get('Database'));
    }
}
