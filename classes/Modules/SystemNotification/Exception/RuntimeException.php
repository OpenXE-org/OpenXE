<?php

namespace Xentral\Modules\SystemNotification\Exception;

use RuntimeException as SplRuntimeException;

class RuntimeException extends SplRuntimeException implements NotificationExceptionInterface
{
}
