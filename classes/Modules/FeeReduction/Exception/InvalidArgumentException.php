<?php

namespace Xentral\Modules\FeeReduction\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements FeeReductionExceptionInterface
{
}
