<?php

declare(strict_types=1);

namespace Xentral\Modules\Ticket\Service;

use Throwable;
use Xentral\Components\Database\Database;
use Xentral\Modules\SystemMailer\Data\EmailBackupAccount;
use Xentral\Modules\Ticket\Exception\InvalidArgumentException;
use Xentral\Modules\Ticket\Exception\NumberGeneratorException;
use Xentral\Modules\Ticket\Wrapper\AddressWrapper;

class TicketService
{
    /** @var Database $db */
    private $db;

    /** @var TicketGateway $gateway */
    private $gateway;

    /** @var AddressWrapper $addressService */
    private $addressService;

    /**
     * TicketService constructor.
     *
     * @param Database       $db
     * @param TicketGateway  $gateway
     * @param AddressWrapper $addressService
     */
    public function __construct(Database $db, TicketGateway $gateway, AddressWrapper $addressService)
    {
        $this->db = $db;
        $this->gateway = $gateway;
        $this->addressService = $addressService;
    }

    /**
     * @param int                $projectId
     * @param EmailBackupAccount $mailAccount
     * @param string             $senderName
     * @param string             $senderAddress
     * @param string             $subject
     * @param int                $timestamp
     * @param string             $replyToName
     * @param string             $replyToAddress
     *
     * @return int
     */
    public function createTicket(
        int $projectId,
        EmailBackupAccount $mailAccount,
        string $senderName,
        string $senderAddress,
        string $subject,
        int $timestamp,
        string $replyToName,
        string $replyToAddress
    ): int
    {
        $assigneeAddressId = $mailAccount->getAddressId();
        if ($assigneeAddressId < 1) {
            $assigneeAddressId = $this->addressService->tryGetAddressIdByEmailAddress(
                $mailAccount->getEmailAddress()
            );
        }
        $ticketNumber = $this->generateRandomTicketNumber();
        if($projectId < 1) {
            $projectId = $mailAccount->getProjectId();
        }
        if ($mailAccount->isTicketMarkAsFinishedEnabled()) {
            $status = TicketGateway::STATUS_COMPLETED;
        } else {
            $status = TicketGateway::STATUS_NEW;
        }

        $insertTicket = "INSERT INTO `ticket` (
                      `schluessel`, `zeit`, `projekt`, `quelle`, `status`, `kunde`,
                      `mailadresse`, `prio`, `betreff`,`warteschlange`,`adresse`
                      ) VALUES (
                        :ticket_number, :date_received, :project_id, :source, :status, :sender_name,
                        :sender_email, :priority, :subject, :queue_id, :address_id);";
        $values = [
            'ticket_number' => $ticketNumber,
            'date_received' => date('Y-m-d H:i:s', $timestamp),
            'project_id' => $projectId,
            'source' => $mailAccount->getEmailAddress(),
            'status' => $status,
            'sender_name' => $senderName,
            'sender_email' => $senderAddress,
            'priority' => 3,
            'subject' => $subject,
            'queue_id' => $mailAccount->getTicketQueueId(),
            'address_id' => $assigneeAddressId,
        ];
        $this->db->perform($insertTicket, $values);
        $ticketId = $this->db->lastInsertId();

        /* todo This should not be included at this point

        $sql = "INSERT INTO `ticket_nachricht` (`id`, `ticket`, `zeit`,`text`,`betreff`,`medium`,`verfasser`, `mail`,`status`,`verfasser_replyto`, `mail_replyto`)
      VALUES (NULL, '$testschluessel', FROM_UNIXTIME($timestamp), '$text','$betreff','$medium','$kunde', '$mailadresse','neu','".$this->app->DB->real_escape_string($verfasser_replyto)."', '".$this->app->DB->real_escape_string($mail_replyto)."');";

        $this->app->DB->InsertWithoutLog($sql);
        $ticket_nachricht_id = $this->app->DB->GetInsertID();

        $this->app->DB->Update("UPDATE ticket AS t 
            INNER JOIN (
              SELECT count(id) as co, ticket 
              FROM ticket_nachricht 
              GROUP BY ticket
            ) AS tn ON t.schluessel = tn.ticket 
            SET t.nachrichten_anz = tn.co 
            WHERE t.schluessel = '$testschluessel'");
        */

        //  todo als rueckgabe ticketnachricht
        return $ticket_nachricht_id;
    }

    /**
     * @param int $ticketMessageId
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function applyTicketRules(int $ticketMessageId): void
    {
        $ticketData = $this->gateway->tryGetTicketDataByByMessage($ticketMessageId);
        if ($ticketData === null) {
            throw new InvalidArgumentException('cannot find ticket by message id');
        }
        $ticketId = $ticketData['id'];
        $senderMail = $ticketData['sender_email'];
        $senderName = $ticketData['sender_name'];
        $subject = $ticketData['subject'];
        $source = $ticketData['source_email'];

        //TODO: richtig loggen: $this->app->erp->LogFile("Empfaengermail: $quelle Sendermail: $mailadresse Kunde: $kunde Betreff: $betreff");
        $ruleArray = $this->gateway->getTicketRules($source, $senderMail, $senderName, $subject);
        if (empty($ruleArray)) {
            return;
        }

        foreach ($ruleArray as $rule) {
            $update = $this->db->update();
            $update->table('ticket');
            if ($rule['is_spam'] === 1) {
                $update->set('inbearbeitung', 0);
                $update->set('zugewiesen', 1);
                $this->db->perform(
                    'UPDATE `ticket_nachricht` SET `status` = :status WHERE `ticket` = :ticket_id',
                    ['status' => TicketGateway::STATUS_SPAM, 'ticket_id' => $ticketId]
                );
            }
            $update->set('dsgvo', $rule['is_gdpr_relevant']);
            $update->set('privat', $rule['is_private'] );
            $update->set('prio', $rule['priority'] );
            $update->where('id = :ticket_id');
            $sql = $update->getStatement();
            $this->db->perform($sql, ['ticket_id' => $ticketId]);
        }
    }

    /**
     * @param string $ticketNumber
     * @param int    $timestamp
     * @param string $message
     * @param string $subject
     * @param string $senderName
     * @param string $senderAddress
     * @param string $status
     * @param string $replyToName
     * @param string $replyToAddress
     *
     * @return int
     */
    public function addTicketMessage(
        string $ticketNumber,
        int $timestamp,
        string $message,
        string $subject,
        string $senderName,
        string $senderAddress,
        string $status,
        string $replyToName,
        string $replyToAddress
    ): int
    {
        $this->db->beginTransaction();
        try {
            $this->markTicketMessagesCompleted($ticketNumber);
            $sql = 'INSERT INTO `ticket_nachricht` (
                        `ticket`, `zeit`, `text`, `betreff`, `medium`,
                        `verfasser`, `mail`,`status`, `verfasser_replyto`, `mail_replyto`
                    ) VALUES (
                        :ticket_number, :date_received, :message, :subject, :medium,
                        :sender_name, :sender_address, :status, :replyto_name, :replyto_address  
                    )';
            $dateString = date('Y-m-d H:i:s', $timestamp);
            $values = [
                'ticket_number'   => $ticketNumber,
                'date_received'   => $dateString,
                'message'         => $message,
                'subject'         => $subject,
                'medium'          => 'email',
                'sender_name'     => $senderName,
                'sender_address'  => $senderAddress,
                'status'          => $status,
                'replyto_name'    => $replyToName,
                'replyto_address' => $replyToAddress,
            ];
            $this->db->perform($sql, $values);
            $messageId = $this->db->lastInsertId();
            $this->updateTicketMessagesCount($ticketNumber);
            $this->resetTicketStatus($ticketNumber);
            $this->db->commit();

            return $messageId;
        } catch (Throwable $e) {
            $this->db->rollBack();
        }
    }

    /**
     * @param string $ticketNumber
     *
     * @return void
     */
    public function markTicketMessagesCompleted(string $ticketNumber): void
    {
      $this->setTicketMessagesStatus($ticketNumber, TicketGateway::STATUS_COMPLETED);
    }

    /**
     * @param string $ticketNumber
     * @param string $status
     *
     * @return void
     */
    private function setTicketMessagesStatus(string $ticketNumber, string $status): void
    {
        $sql = 'UPDATE `ticket_nachricht` SET `status` = :status WHERE `ticket` = :ticket_number';
        $this->db->perform($sql, ['ticket_number' => $ticketNumber, 'status' => $status]);
    }

    /**
     * @param string $ticketNumber
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function resetTicketStatus(string $ticketNumber): void
    {
        $this->ensureTicketNumberExists($ticketNumber);
        $sql = 'UPDATE `ticket` SET 
                    `status` = :status,
                    `zugewiesen` = :is_assigned,
                    `inbearbeitung` = :is_editor_locked
                    WHERE `schluessel` = :ticket_number';
        $values = [
          'status' => TicketGateway::STATUS_NEW,
          'is_assigned' => 0,
          'is_editor_locked' => 0
        ];
        $this->db->perform($sql, $values);
    }

    /**
     * @param string $ticketNumber
     *
     * @return void
     */
    public function updateTicketMessagesCount(string $ticketNumber): void
    {
        $this->ensureTicketNumberExists($ticketNumber);
        $count = $this->gateway->getMessageCountByTicketNumber($ticketNumber);
        $count++;
        $sql = 'UPDATE `ticket` SET `nachrichten_anz` = :message_amount WHERE schluessel = :ticket_number';
        $this->db->perform($sql, ['message_amount' => $count, 'ticket_number' => $ticketNumber]);
    }

    /**
     * @param string $ticketNumber
     *
     * @return void
     */
    private function ensureTicketNumberExists(string $ticketNumber): void
    {
        if (!$this->gateway->existsTicketNumber($ticketNumber)) {
            throw new InvalidArgumentException(
                sprintf('ticket number "%s" does not exist', $ticketNumber)
            );
        }
    }

    /**
     * @throws NumberGeneratorException
     *
     * @return string
     */
    private function generateRandomTicketNumber(): string
    {
        $random = rand(300,700);
        $loopCounter = 0;
        while(true) {
            $candidate = sprintf('%s%04d', date('Ymd'), $random++);
            if (!$this->gateway->existsTicketNumber($candidate)) {
                return $candidate;
            }
            if ($loopCounter > 99) {
                throw new NumberGeneratorException('ticket number generation failed');
            }
            $loopCounter++;
        }
    }
}
