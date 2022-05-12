<?php

namespace Xentral\Modules\DownloadSpooler\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements DownloadSpoolerExceptionInterface
{
}
