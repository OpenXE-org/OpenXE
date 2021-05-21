<?php

namespace Xentral\Modules\Pos\Exception;

use RuntimeException as SplRuntimeException;

class RuntimeException extends SplRuntimeException implements PosExceptionInterface
{
}
