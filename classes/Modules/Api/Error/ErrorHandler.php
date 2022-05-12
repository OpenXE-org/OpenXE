<?php

namespace Xentral\Modules\Api\Error;

use Exception;
use PDOException;
use Xentral\Core\LegacyConfig\Exception\LegacyConfigExceptionInterface;

/**
 * @see /www/api/index.php
 */
class ErrorHandler
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

    /** @var array $errorTypeTranslations */
    private $errorTypeTranslations = [
        E_ERROR             => 'Fatal Error',
        E_PARSE             => 'Parse Error',
        E_CORE_ERROR        => 'Core Error',
        E_COMPILE_ERROR     => 'Compile Error',
        E_USER_ERROR        => 'Fatal User Error',
        E_RECOVERABLE_ERROR => 'Recoverable Error',
    ];

    /**
     * @return void
     */
    public function register()
    {
        register_shutdown_function([$this, 'onShutdown']);

        // Use own error output function
        ini_set('display_errors', true);
        ini_set('display_startup_errors', true);
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

            $content = [
                'error' => [
                    'code'      => ApiError::CODE_UNEXPECTED_ERROR,
                    'message'   => 'Unexpected error',
                    'http_code' => 500,
                ],
            ];

            if ($this->isDebugModeActive()) {
                $errorType = $this->translateErrorType($code);
                $content['debug'] = [
                    'error' => [
                        'message' => $errorType . ': ' . $message,
                        'file'    => $file,
                        'line'    => $line,
                        'code'    => $code,
                    ],
                ];
            }

            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($content);
            exit; // Necessary for E_RECOVERABLE_ERROR
        }

        return true; // Don't execute PHP internal error handler
    }

    /**
     * @param Exception $exception
     */
    public function handleException($exception)
    {
        $errors = [];

        if ($exception instanceof PDOException) {
            if ($exception->getCode() === 'HY000') {
                // "HY000: General error: 1364 Field 'xxxxx' doesn't have a default value"
                if (strpos($exception->getMessage(), 'SQLSTATE[HY000]: General error: 1364') !== false) {
                    $errors[] = str_replace('SQLSTATE[HY000]: General error: 1364 ', '', $exception->getMessage());
                }
            }
            // 42S22: Column not found
            if ($exception->getCode() === '42S22') {
                $errors[] = str_replace('SQLSTATE[42S22]: Column not found: 1054 ', '', $exception->getMessage());
            }
            // 1049: Unknown database
            if ($exception->getCode() === 1049) {
                $errors[] = str_replace('SQLSTATE[HY000] [1049] ', 'DatabaseException: ', $exception->getMessage());
            }
        }

        if ($exception instanceof LegacyConfigExceptionInterface) {
            $errors[] = $exception->getMessage();
        }

        $content = [
            'error' => [
                'code'      => ApiError::CODE_UNEXPECTED_ERROR,
                'message'   => 'Unexpected error',
                'http_code' => 500,
                'errors'    => $errors,
            ],
        ];

        if ($this->isDebugModeActive()) {
            $content['debug'] = [
                'error' => [
                    'message' => 'Unhandled exception: ' . $exception->getMessage(),
                    'file'    => $exception->getFile(),
                    'line'    => $exception->getLine(),
                    'code'    => $exception->getCode(),
                    'trace'   => $exception->getTrace(),
                ],
            ];
        }

        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($content);
        exit;
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
        $type = (int)$type;

        if (!isset($this->errorTypeTranslations[$type])) {
            return 'Unknown Error';
        }

        return $this->errorTypeTranslations[$type];
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

    /**
     * @return bool
     */
    private function isDebugModeActive()
    {
        return defined('DEBUG_MODE') && (int)DEBUG_MODE === 1;
    }
}
