<?php

declare(strict_types=1);

namespace Xentral\Components\MailClient\Exception;

use RuntimeException as SplRuntimeException;

final class LoginException extends SplRuntimeException implements MailClientExceptionInterface
{
}
