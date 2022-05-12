<?php

namespace Xentral\Modules\Backup\Exception;

use RuntimeException as SplRuntimeException;

class RuntimeException extends SplRuntimeException implements BackupExceptionInterface
{
}
