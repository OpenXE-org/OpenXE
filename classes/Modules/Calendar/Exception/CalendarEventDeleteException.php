<?php

namespace Xentral\Modules\Calendar\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class CalendarEventDeleteException extends SplInvalidArgumentException implements CalendarExceptionInterface
{
}
