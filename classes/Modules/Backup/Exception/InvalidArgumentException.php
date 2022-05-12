<?php

namespace Xentral\Modules\Backup\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

/**
 * Class InvalidArgumentException
 *
 * @package Xentral\Modules\Backup\Exception
 */
class InvalidArgumentException extends SplInvalidArgumentException implements BackupExceptionInterface
{
}
