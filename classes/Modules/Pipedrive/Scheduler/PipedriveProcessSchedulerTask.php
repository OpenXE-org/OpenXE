<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Scheduler;

use ArrayObject;
use Xentral\Modules\Pipedrive\Exception\PipedriveConfigurationException;
use Xentral\Modules\Pipedrive\Exception\PipedriveEventException;
use Xentral\Modules\Pipedrive\Exception\PipedriveMetaException;
use Xentral\Modules\Pipedrive\Exception\PipedriveRequestQueuesException;
use Xentral\Modules\Pipedrive\RequestQueues\PipedriveRequestQueuesService;

final class PipedriveProcessSchedulerTask implements PipedriveSchedulerTaskInterface
{
    /** @var string  */
    public const CALL_TYPE = 'pipedrive';

    /** @var PipedriveRequestQueuesService $service */
    private $service;

    /**
     * @param PipedriveRequestQueuesService $service
     */
    public function __construct(PipedriveRequestQueuesService $service)
    {
        $this->service = $service;
    }

    /**
     * @throws PipedriveConfigurationException
     * @throws PipedriveEventException
     * @throws PipedriveMetaException
     * @throws PipedriveRequestQueuesException
     *
     * @return void
     */
    public function execute(): void
    {
        $this->service->execute(self::CALL_TYPE);
    }

    /**
     * @return void
     */
    public function cleanup(): void
    {
        $this->service->cleanup();
    }

    // @codeCoverageIgnoreStart

    /**
     * @inheritDoc
     */
    public function beforeScheduleAction(ArrayObject $data)
    {
        // TODO: Implement beforeScheduleAction() method.
    }

    /**
     * @inheritDoc
     */
    public function afterScheduleAction(ArrayObject $data)
    {
        // TODO: Implement afterScheduleAction() method.
    }

    // @codeCoverageIgnoreEnd
}
