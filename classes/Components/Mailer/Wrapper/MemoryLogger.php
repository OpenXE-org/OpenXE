<?php

namespace Xentral\Components\Mailer\Wrapper;

class MemoryLogger
{
    public $logmessages = [];

    /**
     * @param string $message
     * @param string $dump
     * @param string $module
     * @param string $action
     * @param string $function
     */
    public function LogFile($message, $dump = '', $module = '', $action = '', $function = '')
    {
        $this->logmessages[] = $message;
    }
}
