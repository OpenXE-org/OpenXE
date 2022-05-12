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
        if ($this->isIoncubeError($exception)) {
            $title = $this->handleIoncubeError($exception);
        }

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

    /**
     * @param Throwable $exception
     *
     * @return bool
     */
    private function isIoncubeError($exception)
    {
        if ((int)$exception->getCode() !== E_CORE_ERROR) {
            return false;
        }
        if (strpos($exception->getMessage(), 'requires a license file.') !== false) {
            return true;
        }
        if (strpos($exception->getMessage(), 'ionCube Encoder') !== false) {
            return true;
        }

        return false;
    }

    /**
     * @param Throwable $exception
     *
     * @return string|null
     */
    private function handleIoncubeError($exception)
    {
        $file = $this->extractFileFromIoncubeError($exception);
        if (empty($file)) {
            return null;
        }

        if (!$this->isDeleteableFile($file)) {
            return null;
        }

        @unlink($file);
        if(is_file($file)) {
            return sprintf('Es wurde eine alte Systemdatei gefunden die nicht manuell gelöscht werden konnte. 
            Bitte löschen Sie die Datei %s', $file);
        }
        return 'Es wurde eine alte Systemdatei gefunden und automatisch gelöscht. 
        Bitte führen Sie das Update nochmal durch dann sollte diese Meldung nicht mehr erscheinen.';
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    private function isDeleteableFile(string $file)
    {
        if (!is_file($file)) {
            return false;
        }
        $dir = dirname($file);
        foreach (self::DELETE_FILE_FOLDERS as $folder) {
            if (substr($dir, -strlen($folder)) === $folder) {
                return true;
            }
        }

        return false;
    }

    /**
     * @example "<br>The encoded file <b>/var/www/xentral/www/pages/adresse.php</b> requires a license file.<br>"
     *          "The license file <b>/var/www/xentral/key.php</b> is corrupt."
     *
     * @param Throwable $exception
     *
     * @return string|null
     */
    private function extractFileFromIoncubeError($exception)
    {
        $message = strip_tags($exception->getMessage());
        $theFilePos = stripos($message, 'The File ');
        if ($theFilePos === false) {
            $theFilePos = strpos($message, 'The encoded file');
            if ($theFilePos === false) {
                return null;
            }
            $theFilePos += 16;
        } else {
            $theFilePos += 9;
        }
        $file = trim(substr($message, $theFilePos));
        $file = explode(' ', $file);

        return reset($file);
    }
}
