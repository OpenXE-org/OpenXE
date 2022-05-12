<?php

namespace Xentral\Modules\TOTPLogin;

use Xentral\Core\DependencyInjection\ContainerInterface;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'TOTPLoginService' => 'onInitTOTPLoginService',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return TOTPLoginService
     */
    public static function onInitTOTPLoginService(ContainerInterface $container)
    {
        return new TOTPLoginService(
            $container->get('Database'),
            $container->get('BarcodeFactory'),
            $container->get('TOTPTokenManager')
        );
    }
}
