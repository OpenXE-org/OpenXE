<?php

namespace Xentral\Modules\GoogleApi\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements GoogleApiExceptionInterface
{
}
