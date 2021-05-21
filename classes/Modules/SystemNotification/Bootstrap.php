<?php

namespace Xentral\Modules\SystemNotification;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\SystemNotification\Gateway\NotificationGateway;
use Xentral\Modules\SystemNotification\Service\NotificationService;
use Xentral\Modules\SystemNotification\Service\NotificationServiceInterface;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'NotificationService' => 'onInitNotificationService',
            'NotificationGateway' => 'onInitNotificationGateway',
        ];
    }

    /**
     * @return array
     */
    public static function registerJavascript()
    {
        $baseDir = './classes/Modules/SystemNotification/www/js/';

        return [
            'pushjs'                  => [
                $baseDir . 'pushjs_1.0.8/push.min.js',
                $baseDir . 'pushjs.js',
            ],
            'pushjs_serviceworker.js' => [
                $baseDir . 'pushjs_1.0.8/serviceWorker.min.js',
            ],
            'noty'                    => [
                $baseDir . 'noty_2.4.1/jquery.noty.packaged.min.js',
                $baseDir . 'notify.js',
            ],
        ];
    }

    /**
     * @return array
     */
    public static function registerStylesheets()
    {
        return [
            'notification' => [
                './classes/Modules/SystemNotification/www/css/notification.css',
            ],
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return NotificationServiceInterface
     */
    public static function onInitNotificationService(ContainerInterface $container)
    {
        return new NotificationService($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return NotificationGateway
     */
    public static function onInitNotificationGateway(ContainerInterface $container)
    {
        return new NotificationGateway($container->get('Database'));
    }
}
