<?php

namespace Xentral\Modules\SystemHealth\Exception;

use RuntimeException as SplRuntimeException;

class RuntimeException extends SplRuntimeException implements SystemHealthExceptionInterface
{
}
