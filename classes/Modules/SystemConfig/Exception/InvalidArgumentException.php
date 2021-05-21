<?php

namespace Xentral\Modules\SystemConfig\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements SystemConfigExceptionInterface
{
}
