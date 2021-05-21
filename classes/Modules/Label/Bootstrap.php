<?php

namespace Xentral\Modules\Label;

use Xentral\Core\DependencyInjection\ContainerInterface;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'LabelModule'  => 'onInitLabelModule',
            'LabelService' => 'onInitLabelService',
            'LabelGateway' => 'onInitLabelGateway',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return LabelModule
     */
    public static function onInitLabelModule(ContainerInterface $container)
    {
        return new LabelModule($container->get('LabelService'), $container->get('LabelGateway'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return LabelService
     */
    public static function onInitLabelService(ContainerInterface $container)
    {
        return new LabelService($container->get('Database'), $container->get('LabelGateway'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return LabelGateway
     */
    public static function onInitLabelGateway(ContainerInterface $container)
    {
        return new LabelGateway($container->get('Database'));
    }
}
