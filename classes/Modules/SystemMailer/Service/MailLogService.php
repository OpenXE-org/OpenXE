<?php

namespace Xentral\Modules\SystemMailer\Service;

use Xentral\Components\Database\Database;
use Xentral\Components\Mailer\Data\EmailMessage;
use Xentral\Modules\SystemMailer\Data\MailAccountInterface;

final class MailLogService
{
    /** @var int LOGTYPE_DEFAULT */
    const LOGTYPE_DEFAULT = 0;

    /** @var int LOGTYPE_CC */
    const LOGTYPE_CC = 1;

    /** @var int LOGTYPE_BCC */
    const LOGTYPE_BCC = 2;

    /** @var Database $db */
    private $db;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * @param EmailMessage         $email
     * @param MailAccountInterface $account
     * @param string               $status
     *
     * @return void
     */
    public function logOutgoingMail(EmailMessage $email, MailAccountInterface $account, $status):void
    {
        $status .= sprintf(
            ' (Host:%s User: %s)',
            $account->getServerAddress(),
            $account->getUsername()
        );
        $insert = $this->db->insert();
        $insert->into('mailausgang');
        foreach ($email->getRecipients() as $recipient) {
            $insert->addRow()
                ->cols(
                    [
                        'subject' => $email->getSubject(),
                        'body'    => $email->getBody(),
                        'from'    => $account->getSenderEmailAddress(),
                        'to'      => (string)$recipient,
                        'status'  => $status,
                        'art'     => self::LOGTYPE_DEFAULT,
                    ]
                )
                ->set('zeit', 'NOW()');
        }
        foreach ($email->getCcRecipients() as $recipient) {
            $insert->addRow()
                ->cols(
                    [
                        'subject' => $email->getSubject(),
                        'body'    => $email->getBody(),
                        'from'    => $account->getSenderEmailAddress(),
                        'to'      => (string)$recipient,
                        'status'  => $status,
                        'art'     => self::LOGTYPE_CC,
                    ]
                )
                ->set('zeit', 'NOW()');
        }
        foreach ($email->getBccRecipients() as $recipient) {
            $insert->addRow()
                ->cols(
                    [
                        'subject' => $email->getSubject(),
                        'body'    => $email->getBody(),
                        'from'    => $account->getSenderEmailAddress(),
                        'to'      => (string)$recipient,
                        'status'  => $status,
                        'art'     => self::LOGTYPE_BCC,
                    ]
                )
                ->set('zeit', 'NOW()');
        }

        $sql = $insert->getStatement();
        $values = $insert->getBindValues();

        $this->db->perform($sql, $values);
    }

    /**
     * @param int $days
     *
     * @return int
     */
    public function deleteLogsOlderThan(int $days = 90):int
    {
        $sql = 'DELETE FROM `mailausgang` WHERE DATE_SUB(CURDATE(), INTERVAL :dayInterval DAY) >= zeit';

        return $this->db->fetchAffected($sql, ['dayInterval' => $days]);
    }
}
