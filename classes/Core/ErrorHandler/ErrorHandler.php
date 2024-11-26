<?php

namespace Xentral\Core\ErrorHandler;

use Throwable;

final class ErrorHandler
{
    /** @var array Error types that halts execution */
    const THROWABLE_ERROR_TYPES = [
        E_ERROR, /** @see http://www.bbminfo.com/Tutor/php_error_e_error.php */
        E_PARSE, /** @see http://www.bbminfo.com/Tutor/php_error_e_parse.php */
        E_CORE_ERROR, /** @see http://www.bbminfo.com/Tutor/php_error_e_core_error.php */
        E_COMPILE_ERROR, /** @see http://www.bbminfo.com/Tutor/php_error_e_compile_error.php */
        E_USER_ERROR, /** @see http://www.bbminfo.com/Tutor/php_error_e_user_error.php */
        E_RECOVERABLE_ERROR, /** @see http://www.bbminfo.com/Tutor/php_error_e_recoverable_error.php */
    ];

    /** @var string[]  */
    private const DELETE_FILE_FOLDERS = [
        'www/pages',
        'www/lib/versandarten',
        'www/lib/zahlungsweisen',
    ];

    /**
     * @return void
     */
    public function register()
    {
        register_shutdown_function([$this, 'onShutdown']);

        // Use own error output function
        ini_set('display_errors', true);
        set_error_handler([$this, 'handleError']);

        set_exception_handler([$this, 'handleException']);
    }

    /**
     * @return void
     */
    public function onShutdown()
    {
        $error = error_get_last();
        if ($error === null) {
            return;
        }

        if ($this->isErrorTypeHaltingExecution((int)$error['type'])) {

            // Try to free memory; in case of exhausted memory limit
            @gc_enable();
            @gc_collect_cycles();
            $this->handleError((int)$error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    /**
     * @param int    $code
     * @param string $message
     * @param string $file
     * @param int    $line
     *
     * @return bool
     */
    public function handleError($code, $message, $file, $line)
    {
        if ($this->isErrorTypeHaltingExecution($code)) {
            $type = (string)$this->translateErrorType($code);
            $exception = new PhpErrorException(sprintf('%s: %s', $type, $message), (int)$code);
            $exception->setFile($file);
            $exception->setLine($line);
            $this->handleException($exception);
            die(); // Necessary for E_RECOVERABLE_ERROR
        }

        return true; // Don't execute PHP internal error handler
    }

    /**
     * @param Throwable   $exception
     */
    public function handleException($exception)
    {
        $title = null;
        $data = new ErrorPageData($exception, $title);
        $renderer = new ErrorPageRenderer($data);
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: text/html; charset=utf-8');
        echo $renderer->renderErrorPage();
    }

    /**
     * @see https://secure.php.net/manual/en/errorfunc.constants.php
     *
     * @param int $type
     *
     * @return string|null
     */
    private function translateErrorType($type)
    {
        $errors = [
            E_ERROR             => 'Fatal Error',
            E_PARSE             => 'Parse Error',
            E_CORE_ERROR        => 'Core Error',
            E_COMPILE_ERROR     => 'Compile Error',
            E_USER_ERROR        => 'Fatal User Error',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
        ];

        return $errors[(int)$type];
    }

    /**
     * @param int $type
     *
     * @return bool
     */
    private function isErrorTypeHaltingExecution($type)
    {
        return in_array((int)$type, self::THROWABLE_ERROR_TYPES, true);
    }
}
