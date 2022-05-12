<?php

namespace Xentral\Modules\SystemNotification\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements NotificationExceptionInterface
{
}
