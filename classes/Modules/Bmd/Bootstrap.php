<?php

namespace Xentral\Modules\Bmd;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\Bmd\Service\BmdRevenueLedgerService;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'BmdRevenueLedgerService' => 'onInitBmdRevenueLedgerService',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return BmdRevenueLedgerService
     */
    public static function onInitBmdRevenueLedgerService(ContainerInterface $container)
    {
        return new BmdRevenueLedgerService(
            $container->get('Database')
        );
    }
}