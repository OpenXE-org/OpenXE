<?php

declare(strict_types=1);

namespace Xentral\Modules\Ticket;

use Ticket;
use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\Api\LegacyBridge\LegacyApplication;
use Xentral\Modules\Ticket\Importer\TicketFormatter;
use Xentral\Modules\Ticket\Task\TicketImportHelperFactory;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'TicketFormatter' => 'onInitTicketFormatter',
            'TicketImportHelperFactory' => 'onInitTicketImportHelperFactory',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return TicketFormatter
     */
    public static function onInitTicketFormatter(ContainerInterface $container): TicketFormatter
    {
        return new TicketFormatter();
    }

    /**
     * @param ContainerInterface $container
     *
     * @return TicketImportHelperFactory
     */
    public static function onInitTicketImportHelperFactory(ContainerInterface $container): TicketImportHelperFactory
    {
        /** @var LegacyApplication $app */
        $app = $container->get('LegacyApplication');
        /** @var Ticket $ticketModule */
        $ticketModule = $ticketModule = $app->erp->LoadModul('ticket');

        return new TicketImportHelperFactory(
            $app->DB,
            $app->erp,
            $app->Conf,
            $ticketModule,
            $container->get('TicketFormatter'),
            $container->get('Logger')
        );
    }
}
