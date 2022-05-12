<?php

declare(strict_types=1);

namespace Xentral\Components\Mailer\Transport;

use Xentral\Components\Mailer\Data\EmailMessage;

interface MailerTransportInterface
{
    /** @var string STATUS_PREPARE */
    const STATUS_PREPARE = 'prepare';

    /** @var string STATUS_SUCCSESS */
    const STATUS_SUCCSESS = 'success';

    /** @var string STATUS_ERROR */
    const STATUS_ERROR = 'error';

    /** @var string STATUS_FAILED */
    const STATUS_FAILED = 'failed';

    /**
     * @param EmailMessage $email
     *
     * @return bool success
     */
    public function sendEmail(EmailMessage $email):bool;

    /**
     * @return string
     */
    public function getStatus():string;

    /**
     * @return bool
     */
    public function hasErrors():bool;

    /**
     * @return string[]
     */
    public function getErrorMessages():array;

    /**
     * @return array
     */
    public function getConfigValues():array;
}
