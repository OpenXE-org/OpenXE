<?php

namespace Xentral\Components\Mailer\Wrapper;

use erpAPI;
use Xentral\Components\Logger\LoggerInterface;

/**
 * Anti-Corruption-Layer für erpApi->LogFile()
 */
final class LoggerWrapper implements LoggerInterface
{
    /** @var erpAPI|MemoryLogger $erp */
    private $erp;

    /**
     * @param erpAPI|MemoryLogger $erp
     */
    public function __construct($erp)
    {
        $this->erp = $erp;
    }

    /**
     * @inheritDoc
     */
    public function emergency($message, array $context = [])
    {
        $this->log('emergency', $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function alert($message, array $context = [])
    {
        $this->log('alert', $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function critical($message, array $context = [])
    {
        $this->log('critical', $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function error($message, array $context = [])
    {
        $this->log('error', $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function warning($message, array $context = [])
    {
        $this->log('warning', $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function notice($message, array $context = [])
    {
        $this->log('notice', $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function info($message, array $context = [])
    {
        $this->log('info', $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function debug($message, array $context = [])
    {
        $this->log('debug', $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = [])
    {
        $message = sprintf('%s: %s', strtoupper($level), $message);
        $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $caller = [];
        foreach ($stack as $trace) {
            if ($trace['class'] !== self::class) {
                $caller = $trace;

                break;
            }
        }
        if (!empty($caller)) {
            $csplit = explode('\\', $caller['class']);
            $module = $csplit[count($csplit)-1];
            $function = $caller['function'];
        } else {
            $module = '';
            $function = '';
        }
        $action = '';
        if (array_key_exists('action', $context)) {
            $action = $context['action'];
        }
        $dump = '';
        if (array_key_exists('dump', $context)) {
            $dump = $context['dump'];
            if (is_array($dump)) {
                $dump = print_r($dump, true);
            }
        }
        $this->erp->LogFile($message, $dump, $module, $action, $function);
    }
}
