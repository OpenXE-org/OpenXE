<?php

namespace Xentral\Modules\DownloadSpooler\Exception;

use RuntimeException as SplRuntimeException;

class RuntimeException extends SplRuntimeException implements DownloadSpoolerExceptionInterface
{
}
