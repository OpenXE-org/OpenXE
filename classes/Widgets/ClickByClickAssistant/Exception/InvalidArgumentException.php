<?php

declare(strict_types=1);

namespace Xentral\Widgets\ClickByClickAssistant\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements ClickByClickAssistantExceptionInterface
{
}
