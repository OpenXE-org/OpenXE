<?php

declare(strict_types=1);

namespace Xentral\Modules\CopperSurcharge\Exception;

use RuntimeException;

final class EmptyResultException extends RuntimeException implements CopperSurchargeExceptionInterface
{
}
