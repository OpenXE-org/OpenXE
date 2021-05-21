<?php

declare(strict_types=1);

namespace Xentral\Modules\TimeManagement\Wrapper;

use erpAPI;
use Xentral\Modules\TimeManagement\Exception\EmailNotSentException;
use Xentral\Modules\TimeManagement\Exception\EmailAccountNotFoundException;

final class TimeManagementMailerWrapper
{

    /** @var erpAPI erp */
    private $erp;

    /**
     * @param erpAPI $erp
     */
    public function __construct(erpAPI $erp)
    {
        $this->erp = $erp;
    }

    /**
     * @param string   $senderEmail
     * @param string[] $recipientEmails
     * @param string   $mailSubject
     * @param string   $mailContent
     *
     * @throws EmailAccountNotFoundException
     * @throws EmailNotSentException
     */
    public function send(string $senderEmail, array $recipientEmails, string $mailSubject, string $mailContent): void
    {
        foreach ($recipientEmails as $email) {
            $isSent = $this->erp->MailSend(
                $this->erp->GetFirmaMail(),
                $senderEmail,
                $email,
                '',
                $mailSubject,
                $mailContent,
                "",
                ""
            );
            if (!$isSent) {
                throw new EmailNotSentException('Mail could not be sent. More info in the logger');
            }
        }
    }
}
