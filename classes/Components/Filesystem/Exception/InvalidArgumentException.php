<?php

namespace Xentral\Components\Filesystem\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements FilesystemExceptionInterface
{
}
