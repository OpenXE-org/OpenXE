<?php

declare(strict_types=1);

namespace Xentral\Components\Mailer;

use Xentral\Components\Mailer\Data\EmailMessage;
use Xentral\Components\Mailer\Transport\MailerTransportInterface;

class Mailer implements MailerInterface
{
    /** @var MailerTransportInterface $transport */
    private $transport;

    public function __construct(MailerTransportInterface $transport)
    {
        $this->transport = $transport;
    }

    /**
     * @param EmailMessage $email
     *
     * @return bool
     */
    public function send(EmailMessage $email): bool
    {
        return $this->transport->sendEmail($email);
    }
}
