<?php

declare(strict_types=1);

namespace Xentral\Modules\CopperSurcharge\Exception;

use InvalidArgumentException;

final class InvalidDateFormatException extends InvalidArgumentException implements CopperSurchargeExceptionInterface
{
}
