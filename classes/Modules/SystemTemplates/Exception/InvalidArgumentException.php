<?php

namespace Xentral\Modules\SystemTemplates\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

/**
 * Class InvalidArgumentException
 *
 * @package Xentral\Modules\SystemTemplates\Exception
 */
class InvalidArgumentException extends SplInvalidArgumentException implements SystemTemplatesExceptionInterface
{
}
