<?php

namespace Xentral\Modules\Calendar\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class CalendarEventSaveException extends SplInvalidArgumentException implements CalendarExceptionInterface
{
}
