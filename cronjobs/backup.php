<?php

use Xentral\Components\Backup\Logger\BackupLog;
use Xentral\Modules\Backup\Scheduler\BackupScheduleTask;
use Xentral\Modules\Backup\Scheduler\Adapter\SchedulerAdapter;

$backupTask = $app->Container->get('BackupScheduleTask');

/** @var BackupScheduleTask $adapter */
$adapter = new SchedulerAdapter($backupTask);

try {
  //$adapter->debugMode = true;
  $adapter->execute();
  $adapter->cleanup();
} catch (Throwable $exception) {
  /** @var BackupLog $logger */
  $logger = $app->Container->get('BackupLog');
  $logger->write('ERROR');
  $adapter->cleanup();
  throw new RuntimeException($exception->getMessage());
}