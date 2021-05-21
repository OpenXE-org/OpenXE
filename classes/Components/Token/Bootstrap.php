<?php

namespace Xentral\Components\Token;

use Xentral\Core\DependencyInjection\ContainerInterface;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'TOTPTokenManager' => 'onInitTOTPTokenManager',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return TOTPTokenManager
     */
    public static function onInitTOTPTokenManager(ContainerInterface $container)
    {
        return new TOTPTokenManager();
    }
}
