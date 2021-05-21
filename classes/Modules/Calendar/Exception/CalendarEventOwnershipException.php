<?php

namespace Xentral\Modules\Calendar\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class CalendarEventOwnershipException extends SplInvalidArgumentException implements CalendarExceptionInterface
{
}
