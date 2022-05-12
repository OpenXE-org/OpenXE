<?php

declare(strict_types=1);

namespace Xentral\Components\MailClient\Client;

use Xentral\Components\Mailer\Data\EmailMessage;
use Xentral\Components\Mailer\Data\EmailRecipient;

interface MimeMessageFormatterInterface
{
    /**
     * @param EmailMessage   $email
     * @param EmailRecipient $sender
     * @param string|null    $messageId
     *
     * @return string
     */
    public function formatMessage(EmailMessage $email, EmailRecipient $sender, string $messageId = null): string;
}
