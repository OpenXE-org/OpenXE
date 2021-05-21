<?php

namespace Xentral\Components\Util\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements UtilExceptionInterface
{
}
