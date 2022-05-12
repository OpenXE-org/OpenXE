<?php

declare(strict_types=1);

namespace Xentral\Components\MailClient\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

final class InvalidArgumentException extends SplInvalidArgumentException implements MailClientExceptionInterface
{
}
