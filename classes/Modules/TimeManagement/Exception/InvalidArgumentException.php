<?php

declare(strict_types=1);

namespace Xentral\Modules\TimeManagement\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

final class InvalidArgumentException extends SplInvalidArgumentException implements TimeManagementExceptionInterface
{
}
