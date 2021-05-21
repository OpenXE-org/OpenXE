<?php

declare(strict_types=1);

namespace Xentral\Modules\SubscriptionCycle\Scheduler;


interface TaskMutexServiceInterface
{
    /**
     * @param string $parameter
     * @param bool   $active
     */
    public function setMutex(string $parameter, bool $active = true): void;

    /**
     * @param string $parameter
     *
     * @return bool
     */
    public function isTaskInstanceRunning(string $parameter): bool;
}
