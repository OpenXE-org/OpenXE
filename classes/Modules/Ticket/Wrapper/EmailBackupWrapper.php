<?php

declare(strict_types=1);

namespace Xentral\Modules\Ticket\Wrapper;

use Xentral\Components\Database\Database;
use Xentral\Modules\Ticket\Exception\InvalidArgumentException;

class EmailBackupWrapper
{
    /** @var Database $db */
    private $db;

    /**
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param int    $emailAccountId
     * @param string $subject
     * @param string $sender
     * @param string $plainTextBody
     * @param string $htmlBody
     * @param int    $timestamp
     * @param bool   $hasAttachment
     *
     * @return int
     */
    public function createEmailBackup(
        int $emailAccountId,
        string $subject,
        string $sender,
        string $plainTextBody,
        string $htmlBody,
        int $timestamp,
        bool $hasAttachment
    ): int {
        $sql = 'INSERT INTO `emailbackup_mails` (
                     `webmail`, `subject`, `sender`, `action`, `action_html`,
                     `empfang`, `anhang`, `checksum`
                     ) VALUES (
                     :mail_account_id, :subject, :sender, :action, :action_html,
                     :date_received, :has_attachment, :checksum
                     )';
        $date = date('Y-m-d H:i:s', $timestamp);
        $checksum = md5($sender . $subject . $timestamp);
        $values = [
            'mail_account_id' => $emailAccountId,
            'subject'         => $subject,
            'sender'          => $sender,
            'action'          => $plainTextBody,
            'action_html'     => $htmlBody,
            'date_received'   => $date,
            'has_attachment'  => (int)$hasAttachment,
            'checksum'        => $checksum,
        ];
        $this->db->perform($sql, $values);

        return $this->db->lastInsertId();
    }

    /**
     * @param int    $emailAccountId
     * @param string $sender
     * @param string $subject
     * @param int    $timestamp
     *
     * @return bool
     */
    public function existsEmailBackup(int $emailAccountId, string $sender, string $subject, int $timestamp): bool
    {
        $date = date('Y-m-d H:i:s', $timestamp);
        $checksum = md5($sender . $subject . $timestamp);
        $sql = 'SELECT COUNT(eb.id)
                FROM `emailbackup_mails` AS `eb`
                WHERE eb.checksum = :checksum AND eb.empfang = :date_received AND eb.webmail = :email_account_id';
        $values = [
            'checksum'         => $checksum,
            'date_received'    => $date,
            'email_account_id' => $emailAccountId,
        ];
        $entriesCount = $this->db->fetchValue($sql, $values);

        return $entriesCount !== null && $entriesCount > 0;
    }

    /**
     * @param int    $emailBackupId
     * @param string $message
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function addTicketMessageToEmailBackup(int $emailBackupId, string $message): void
    {
        if (!$this->existsEmailBackupId($emailBackupId)) {
            throw new InvalidArgumentException('email backup does not exits');
        }
        $sql = 'UPDATE `emailbackup_mails` SET `ticketnachricht` = :message WHERE `id` = :id';
        $this->db->perform($sql, ['message' => $message, 'id' => $emailBackupId]);
    }

    /**
     * @param int $emailBackupId
     *
     * @return bool
     */
    public function existsEmailBackupId(int $emailBackupId): bool
    {
        $sql = 'SELECT COUNT(eb.id)
                FROM `emailbackup_mails` AS `eb` WHERE eb.id = :id';
        $id = $this->db->fetchValue($sql, ['id' => $emailBackupId]);

        return $id === $emailBackupId;
    }
}
