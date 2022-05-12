<?php

namespace Xentral\Modules\Hubspot\Scheduler;

use ArrayObject;

interface HubspotSchedulerTaskInterface
{
    public function execute();

    public function cleanup();

    public function beforeScheduleAction(ArrayObject $args);

    public function afterScheduleAction(ArrayObject $args);
}
