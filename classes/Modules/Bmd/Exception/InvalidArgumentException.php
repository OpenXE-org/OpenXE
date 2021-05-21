<?php

namespace Xentral\Modules\Bmd\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements BmdExceptionInterface
{
}