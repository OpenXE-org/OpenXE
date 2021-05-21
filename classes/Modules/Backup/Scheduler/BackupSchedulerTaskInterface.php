<?php

namespace Xentral\Modules\Backup\Scheduler;

use ArrayObject;

interface BackupSchedulerTaskInterface
{
    public function execute();

    public function cleanup();

    public function beforeScheduleAction(ArrayObject $args);

    public function afterScheduleAction(ArrayObject $args);
}
