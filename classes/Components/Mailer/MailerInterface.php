<?php

namespace Xentral\Components\Mailer;

use Xentral\Components\Mailer\Data\EmailMessage;
use Xentral\Components\Mailer\Transport\MailerTransportInterface;

interface MailerInterface
{
    /**
     * @param MailerTransportInterface $transport
     */
    public function __construct(MailerTransportInterface $transport);

    /**
     * @param EmailMessage $email
     *
     * @return bool
     */
    public function send(EmailMessage $email);
}
