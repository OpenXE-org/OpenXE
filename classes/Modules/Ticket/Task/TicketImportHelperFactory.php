<?php

declare(strict_types=1);

namespace Xentral\Modules\Ticket\Task;

use DB as LegacyDataBase;
use Ticket as TicketModule;
use erpAPI;
use Config as LegacyConfig;

use Xentral\Components\Logger\Logger;
use Xentral\Components\MailClient\Client\MailClientInterface;
use Xentral\Modules\SystemMailer\Data\EmailBackupAccount;
use Xentral\Modules\Ticket\Importer\TicketFormatter;

class TicketImportHelperFactory
{
    /** @var LegacyDataBase $db */
    private $db;

    /** @var erpAPI $erpApi */
    private $erpApi;

    /** @var LegacyConfig $config */
    private $config;

    /** @var TicketModule $ticketModule */
    private $ticketModule;

    /** @var TicketFormatter $formatter */
    private $formatter;

    /** @var Logger $logger */
    private $logger;

    /**
     * TicketImportHelperFactory constructor.
     *
     * @param LegacyDataBase  $db
     * @param erpAPI          $erpApi
     * @param LegacyConfig    $config
     * @param TicketModule    $ticketModule
     * @param TicketFormatter $formatter
     * @param Logger          $logger
     */
    public function __construct(
        LegacyDataBase $db,
        erpAPI $erpApi,
        LegacyConfig $config,
        TicketModule $ticketModule,
        TicketFormatter $formatter,
        Logger $logger
    ) {
        $this->db = $db;
        $this->erpApi = $erpApi;
        $this->ticketModule = $ticketModule;
        $this->config = $config;
        $this->formatter = $formatter;
        $this->logger = $logger;
    }

    /**
     * @param MailClientInterface $mailClient
     * @param EmailBackupAccount  $mailAccount
     * @param int                 $projectId
     *
     * @return TicketImportHelper
     */
    public function create(
        MailClientInterface $mailClient,
        EmailBackupAccount $mailAccount,
        int $projectId
    ): TicketImportHelper
    {
        $helper = new TicketImportHelper(
            $this->db,
            $this->erpApi,
            $this->ticketModule,
            $this->config,
            $this->formatter,
            $mailClient,
            $mailAccount,
            $projectId
        );
        $helper->setLogger($this->logger);

        return $helper;
    }
}
