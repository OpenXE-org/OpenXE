<?php

declare(strict_types=1);

namespace Xentral\Modules\Ticket\Importer;

use DateTimeInterface;
use Xentral\Components\MailClient\Client\MailClientInterface;
use Xentral\Modules\SystemMailer\Data\EmailBackupAccount;
use Xentral\Modules\Ticket\Service\TicketGateway;
use Xentral\Modules\Ticket\Service\TicketService;
use Xentral\Modules\Ticket\Wrapper\EmailBackupWrapper;

class InboxTicketImporter
{
    /** @var TicketGateway $gateway */
    private $gateway;

    /** @var TicketService $service */
    private $service;

    /** @var TicketFormatter $formatter */
    private $formatter;

    /** @var EmailBackupWrapper $emailBackupService */
    private $emailBackupService;

    /**
     * @param TicketGateway      $gateway
     * @param TicketService      $service
     * @param TicketFormatter    $formatter
     * @param EmailBackupWrapper $emailBackupService
     */
    public function __construct(
        TicketGateway $gateway,
        TicketService $service,
        TicketFormatter $formatter,
        EmailBackupWrapper $emailBackupService
    ) {
        $this->gateway = $gateway;
        $this->service = $service;
        $this->formatter = $formatter;
        $this->emailBackupService = $emailBackupService;
    }

    /**
     * @param MailClientInterface    $client
     * @param DateTimeInterface|null $fromDate
     *
     * @return string[]|array
     */
    public function searchImportableEmails(MailClientInterface $client, DateTimeInterface $fromDate = null): array
    {
        if ($fromDate !== null) {
            $criteria = sprintf('UNSEEN SINCE %s', $fromDate->format('d-M-Y'));
        } else {
            $criteria = 'UNSEEN';
        }

        return $client->searchMessages($criteria);
    }

    /**
     * @param EmailBackupAccount  $mailAccount
     * @param MailClientInterface $client
     * @param int                 $messageId
     * @param int                 $userId
     *
     * @return void
     */
    public function importTicket(
        EmailBackupAccount $mailAccount,
        MailClientInterface $client,
        int $messageId,
        int $userId
    ): void {
        //fetch message
        $message = $client->fetchMessage($messageId);

        //format several values
        $sender = $message->getSender()->getEmail();
        $subject = $message->getSubject();
        $timestamp = $message->getDate()->getTimestamp();
        $attachments = $message->getAttachments();
        $plainText = $message->getPlainTextBody();
        if ($plainText === null) {
            $plainText = '';
        }
        $htmlText = $message->getHtmlBody();
        if (empty($htmlText)) {
            $htmlText = nl2br($plainText);
        }

        if (
            $this->emailBackupService->existsEmailBackup(
                $mailAccount->getId(),
                $sender,
                $subject,
                $timestamp
            )
        ) {
            $emailBackupId = $this->emailBackupService->createEmailBackup(
                $mailAccount->getId(),
                $subject,
                $sender,
                $plainText,
                $htmlText,
                $timestamp,
                count($attachments) > 0
            );
            //TODO: store attachment files (filesystem)
        }

        //extract ticket number from email subject
        $ticketNumber = $this->tryGetTicketNumberFromSubject($subject);

        if ($ticketNumber !== null && !$this->gateway->existsTicketNumber($ticketNumber)) {
            $projectId = $mailAccount->getProjectId() !== 0
                ? $mailAccount->getProjectId()
                : 1;
            $ticketId = $this->service->createTicket();
        }
        if ($ticketNumber !== null) {
            $this->service->addTicketMessage(
                $ticketNumber,
                $timestamp,
                $htmlText,
                $subject,
                $message->getSender()->getName(),
                $message->getSender()->getEmail(),
                TicketGateway::STATUS_NEW,
                $message->getSender()->getName(),
                $message->getReplyToAddress()
            );
        }

        //if the email was a ticket related mark the emailbackup as ticket related
        if ($ticketNumber !== null) {
            //mark emailbackup as ticketmessage
            //save ticket headers
        }

        if (/* autoresponder is active */true) {
            //send autoresponse
        }

        if (/* email delete is active */true) {
            //delete email
        }
    }

    /**
     * @param string $subject
     *
     * @return string|null
     */
    private function tryGetTicketNumberFromSubject(string $subject): ?string
    {
        if (!preg_match("/Ticket #(\d{12})/i", $subject, $matches)) {
            return null;
        }

        return $matches[1];
    }
}
