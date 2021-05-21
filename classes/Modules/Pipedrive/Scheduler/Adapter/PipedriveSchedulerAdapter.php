<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Scheduler\Adapter;

use ArrayObject;
use Xentral\Components\Logger\LoggerAwareTrait;
use Xentral\Modules\Pipedrive\Exception\PipedriveSchedulerAdapterBadMethodException;
use Xentral\Modules\Pipedrive\Scheduler\PipedriveSchedulerTaskInterface;

final class PipedriveSchedulerAdapter
{
    use LoggerAwareTrait;

    /** @var PipedriveSchedulerTaskInterface $schedulerTask */
    private $schedulerTask;

    /** @var bool $debugMode */
    public $debugMode = false;

    /**
     * PipedriveSchedulerAdapter constructor.
     *
     * @param PipedriveSchedulerTaskInterface $schedulerTask
     */
    public function __construct(PipedriveSchedulerTaskInterface $schedulerTask)
    {
        $this->schedulerTask = $schedulerTask;
    }

    /**
     * @param $method
     * @param $args
     *
     * @throws PipedriveSchedulerAdapterBadMethodException
     *
     * @return void
     */
    public function __call($method, $args)
    {
        if (!method_exists($this->schedulerTask, $method)) {
            $class = get_class($this->schedulerTask);
            throw new PipedriveSchedulerAdapterBadMethodException(
                sprintf('Method %s at %s class is missing', $method, $class)
            );
        }

        if (!is_callable([$this->schedulerTask, $method])) {
            $class = get_class($this->schedulerTask);
            throw new PipedriveSchedulerAdapterBadMethodException(
                sprintf('No callable method %s at %s class', $method, $class)
            );
        }

        $this->debug(json_encode(new ArrayObject($args)));

        if ($method === 'execute' && empty($args) === true) {
            $this->schedulerTask->beforeScheduleAction(new ArrayObject($args));
        }

        if ($this->debugMode === true) {
            $message = 'Call ' . get_class($this->schedulerTask) . '::' . $method . ' with args ' . json_encode($args);
            $this->debug($message);
        }

        call_user_func([$this->schedulerTask, $method], $args);

        if ($method === 'execute' && empty($args) === true) {
            $this->schedulerTask->afterScheduleAction(new ArrayObject($args));
        }
    }


    /**
     * @param string $message
     *
     * @return void
     */
    public function debug(string $message): void
    {
        if ($this->debugMode === false) {
            return;
        }
        $this->logger->debug(date('Y-m-d H:i:s') . '- ' . $message);
    }

}
