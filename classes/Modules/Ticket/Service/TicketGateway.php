<?php

declare(strict_types=1);

namespace Xentral\Modules\Ticket\Service;

use Xentral\Components\Database\Database;
use Xentral\Components\Database\SqlQuery\SelectQuery;

class TicketGateway
{
    /** @var string STATUS_NEW */
    public const STATUS_NEW = 'neu';

    /** @var string STATUS_COMPLETED */
    public const STATUS_COMPLETED = 'abgeschlossen';

    /** @var string STATUS_SPAM */
    public const STATUS_SPAM = 'spam';

    /** @var Database $db */
    private $db;

    /**
     * TicketGateway constructor.
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $ticketNumber
     *
     * @return bool
     */
    public function existsTicketNumber(string $ticketNumber): bool
    {
        return $this->tryGetTicketDataByTicketNumber($ticketNumber) !== null;
    }

    /**
     * @param string $key
     *
     * @return ?array
     */
    public function tryGetTicketDataByTicketNumber(string $key): ?array
    {
        $sql = $this->createTicketQuery()
            ->where('t.schluessel like :ticket_number')
            ->where("t.schluessel <> ''")
            ->getStatement();
        $row = $this->db->fetchRow($sql, ['ticket_number' => $key]);
        if (empty($row)) {
            return null;
        }

        return $row;
    }

    /**
     * @return SelectQuery
     */
    private function createTicketQuery(): SelectQuery
    {
        return $this->db->select()
            ->cols(
                [
                    't.id',
                    't.schluessel as ticket_number',
                    't.zeit as timestamp',
                    't.projekt as project',
                    't.bearbeiter as editor',
                    't.quelle as source_email',
                    't.status',
                    't.adresse as address_id',
                    't.kunde as customer',
                    't.warteschlange as queue_name',
                    't.mailadresse as sender_email',
                    't.prio as priority',
                    't.betreff as subject',
                    't.zugewiesen as is_assigned',
                    't.inbearbeitung as is_editor_locked',
                    't.inbearbeitung_user as editor_user',
                    't.firma as company_id',
                    't.notiz as note',
                    't.bitteantworten as is_response_required',
                    't.service',
                    't.kommentar as comment',
                    't.privat as is_private',
                    't.dsgvo as is_gdpr_relevant',
                    't.tags',
                    't.nachrichten_anz as count_messages',
                ]
            )
            ->from('ticket as t');
    }

    /**
     * @param string $recipientMail
     * @param string $senderMail
     * @param string $senderName
     * @param string $subject
     *
     * @return array
     */
    public function getTicketRules(
        string $recipientMail,
        string $senderMail,
        string $senderName,
        string $subject
    ): array {
        $sql = "SELECT
            tr.spam AS `is_spam`,
            tr.dsgvo AS `is_gdpr_relevant`,
            tr.prio AS `priority`,
            tr.persoenlich AS `is_private`,
            tr.warteschlange AS `queue_id`
        FROM `ticket_regeln` AS `tr`
        WHERE
          tr.aktiv = 1
          AND (tr.empfaenger_email LIKE :source_email OR empfaenger_email = '')
          AND (
              tr.sender_email LIKE :sender_email
              OR (tr.sender_email LIKE '@%' AND :sender_email LIKE CONCAT('%', tr.sender_email))
              OR tr.sender_email = ''
          )
          AND (tr.name LIKE :sender_name OR tr.name = '')
          AND (tr.betreff LIKE :subject OR tr.betreff = '')";
        $values = [
            'source_email' => $recipientMail,
            'sender_email' => $senderMail,
            'sender_name'  => $senderName,
            'subject'      => $subject,
        ];

        return $this->db->fetchAll($sql, $values);
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function existsTicketId(int $id): bool
    {
        return $this->tryGetTicketById($id) !== null;
    }

    /**
     * @param int $id
     *
     * @return array|null
     */
    public function tryGetTicketById(int $id): ?array
    {
        if ($id < 1) {
            return null;
        }
        $sql = $this->createTicketQuery()
            ->where('t.id like :id')
            ->getStatement();
        $row = $this->db->fetchRow($sql, ['id' => $id]);
        if (empty($row)) {
            return null;
        }

        return $row;
    }

    /**
     * @param int $messageId
     *
     * @return array|null
     */
    public function tryGetTicketDataByByMessage(int $messageId): ?array
    {
        if ($messageId < 1) {
            return null;
        }
        $sql = $this->createTicketQuery()
            ->join('', 'ticket_nachricht AS tm', 'tm.ticket = t.id')
            ->where('tm.id = :id')
            ->getStatement();
        $row = $this->db->fetchRow($sql, ['id' => $messageId]);
        if (empty($row)) {
            return null;
        }

        return $row;
    }

    /**
     * @param string $ticketNumber
     *
     * @return int
     */
    public function getMessageCountByTicketNumber(string $ticketNumber): int
    {
        $sql = 'SELECT COUNT(tm.id) FROM `ticket_nachricht` AS `tm` WHERE tm.ticket = :ticket_number';
        $count = $this->db->fetchValue($sql, ['ticket_number' => $ticketNumber]);
        if ($count === null) {
            return 0;
        }

        return $count;
    }
}
