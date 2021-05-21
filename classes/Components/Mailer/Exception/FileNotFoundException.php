<?php

namespace Xentral\Components\Mailer\Exception;

use RuntimeException;

class FileNotFoundException extends RuntimeException implements MailerExceptionInterface
{
}
