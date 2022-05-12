<?php

namespace Xentral\Modules\Backup\Scheduler;

use RuntimeException;
use Xentral\Modules\Backup\Exception\BackupExceptionInterface;

class BackupSchedulerException extends RuntimeException implements BackupExceptionInterface
{

}
