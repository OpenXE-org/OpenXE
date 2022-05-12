<?php

namespace Xentral\Modules\Company\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements DocumentCustomizationExceptionInterface
{
}
