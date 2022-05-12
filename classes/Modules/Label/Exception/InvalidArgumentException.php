<?php

namespace Xentral\Modules\Label\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements LabelExceptionInterface
{
}
