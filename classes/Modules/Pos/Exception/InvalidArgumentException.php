<?php

namespace Xentral\Modules\Pos\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements PosExceptionInterface
{
}
