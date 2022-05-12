<?php

namespace Xentral\Modules\Hubspot\Scheduler;

use ArrayObject;
use Xentral\Modules\Hubspot\RequestQueues\HubspotRequestQueuesService;
use Xentral\Modules\SubscriptionCycle\Scheduler\TaskMutexServiceInterface;

final class HubspotProcessSchedulerTask implements HubspotSchedulerTaskInterface
{
    const CALL_TYPE = 'hubspot';
    /** @var HubspotRequestQueuesService $gateway */
    private $service;

    /** @var TaskMutexServiceInterface $taskMutexService */
    private $taskMutexService;

    /**
     * @param HubspotRequestQueuesService $service
     * @param TaskMutexServiceInterface   $taskMutexService
     */
    public function __construct(HubspotRequestQueuesService $service, TaskMutexServiceInterface $taskMutexService)
    {
        $this->service = $service;
        $this->taskMutexService = $taskMutexService;
    }

    /**
     * @return void
     */
    public function execute()
    {
        if ($this->taskMutexService->isTaskInstanceRunning('hubspot_process')) {
            return;
        }
        $this->taskMutexService->setMutex('hubspot_process');

        $this->service->execute(static::CALL_TYPE);
    }

    /**
     * @return void
     */
    public function cleanup()
    {
        $this->taskMutexService->setMutex('hubspot_process', false);
        $this->service->cleanup();
    }

    /**
     * @param ArrayObject $args
     *
     * @return void
     */
    public function beforeScheduleAction(ArrayObject $args)
    {
        // TODO: Implement beforeScheduleAction() method.
    }

    /**
     * @param ArrayObject $args
     *
     * @return void
     */
    public function afterScheduleAction(ArrayObject $args)
    {
        // TODO: Implement afterScheduleAction() method.
    }
}
