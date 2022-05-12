<?php

namespace Xentral\Components\Mailer\Exception;

use RuntimeException;

class InvalidArgumentException extends RuntimeException implements MailerExceptionInterface
{
}
