<?php

namespace Xentral\Modules\SystemConfig\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class ValueTooLargeException extends SplInvalidArgumentException implements SystemConfigExceptionInterface
{
}
