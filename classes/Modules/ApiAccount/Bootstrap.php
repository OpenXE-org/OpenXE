<?php

namespace Xentral\Modules\ApiAccount;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\ApiAccount\Service\ApiAccountService;


class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices():array
    {
        return [
            'ApiAccountService'          => 'onInitApiAccountService'
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ApiAccountService
     */
    public static function onInitApiAccountService(ContainerInterface $container):ApiAccountService
    {
        return new ApiAccountService(
            $container->get('Database'),
            $container->get('SystemConfigModule'),
            $container->get('Logger')
        );
    }
}
