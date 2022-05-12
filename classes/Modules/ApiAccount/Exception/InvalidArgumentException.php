<?php

namespace Xentral\Modules\ApiAccount\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements ApiAccountExceptionInterface
{
}
