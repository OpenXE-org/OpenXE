<?php

namespace Xentral\Modules\Calendar\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements CalendarExceptionInterface
{
}
