<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Scheduler;

use ArrayObject;

interface PipedriveSchedulerTaskInterface
{

    /**
     * @return void
     */
    public function execute(): void;

    /**
     * @return void
     */
    public function cleanup(): void;

    /**
     * @param ArrayObject $data
     *
     * @return mixed
     */
    public function beforeScheduleAction(ArrayObject $data);

    /**
     * @param ArrayObject $data
     *
     * @return mixed
     */
    public function afterScheduleAction(ArrayObject $data);
}
