<?php

declare(strict_types=1);

namespace Xentral\Modules\SystemMailer\Service;

use Xentral\Components\Mailer\Data\EmailMessage;

final class MailBodyCleaner
{
    /**
     * @param EmailMessage $email
     *
     * @return EmailMessage
     */
    public function cleanEmailBody(EmailMessage $email): EmailMessage
    {
        return $email;
    }
}
