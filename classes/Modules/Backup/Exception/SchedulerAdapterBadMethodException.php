<?php

namespace Xentral\Modules\Backup\Exception;

use BadMethodCallException as SplBadMethodCallException;

final class SchedulerAdapterBadMethodException extends SplBadMethodCallException implements BackupExceptionInterface
{

}
