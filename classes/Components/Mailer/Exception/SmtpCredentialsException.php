<?php

namespace Xentral\Components\Mailer\Exception;

use RuntimeException;

class SmtpCredentialsException extends RuntimeException implements MailerExceptionInterface
{
}
