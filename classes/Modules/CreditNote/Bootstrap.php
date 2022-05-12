<?php

namespace Xentral\Modules\CreditNote;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\CreditNote\Service\CreditNoteAddressService;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'CreditNoteAddressService' => 'onInitCreditNoteAddressService',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return CreditNoteAddressService
     */
    public static function onInitCreditNoteAddressService(ContainerInterface $container)
    {
        return new CreditNoteAddressService($container->get('Database'));
    }
}
