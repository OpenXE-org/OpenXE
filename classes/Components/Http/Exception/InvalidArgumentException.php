<?php

namespace Xentral\Components\Http\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements HttpComponentExceptionInterface
{
}
