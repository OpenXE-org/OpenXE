<?php

namespace Xentral\Modules\Backup\Scheduler\Adapter;

use ArrayObject;
use Xentral\Modules\Backup\Exception\SchedulerAdapterBadMethodException;
use Xentral\Modules\Backup\Scheduler\BackupSchedulerTaskInterface;

final class SchedulerAdapter
{

    public $debugMode = false;

    /**
     * @var BackupSchedulerTaskInterface
     */
    private $schedulerTask;

    public function __construct(BackupSchedulerTaskInterface $schedulerTask)
    {
        $this->schedulerTask = $schedulerTask;
    }

    public function __call($method, $args)
    {
        if (!method_exists($this->schedulerTask, $method)) {
            $class = get_class($this->schedulerTask);
            throw new SchedulerAdapterBadMethodException(sprintf('Method %s at %s class is missing', $method, $class));
        }
        if (is_callable([$this->schedulerTask, $method])) {
            $this->debug(json_encode(new ArrayObject($args)));
            $this->schedulerTask->beforeScheduleAction(new ArrayObject($args));
            if ($this->debugMode === true) {
                $message = 'Call ' . get_class($this->schedulerTask) . '::' . $method . ' with args ' . json_encode($args);
                $this->debug($message);
            }
            call_user_func([$this->schedulerTask, $method], $args);
            $this->schedulerTask->afterScheduleAction(new ArrayObject($args));
        } else {
            $class = get_class($this->schedulerTask);
            throw new SchedulerAdapterBadMethodException(sprintf('No callable method %s at %s class', $method, $class));
        }
    }

    /**
     * @param string      $message
     * @param null|string $debuggerFile
     *
     * @return null|void
     */
    public function debug($message, $debuggerFile = null)
    {
        if ($this->debugMode === false) {
            return null;
        }
        $logFile = null === $debuggerFile ? sys_get_temp_dir() . '/backup_debug.log' : $debuggerFile;
        file_put_contents($logFile, date('Y-m-d H:i:s') . '- ' . $message . "\n", FILE_APPEND | LOCK_EX);
    }
}
