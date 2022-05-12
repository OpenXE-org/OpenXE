<?php

namespace Xentral\Modules\SystemHealth\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements SystemHealthExceptionInterface
{
}
