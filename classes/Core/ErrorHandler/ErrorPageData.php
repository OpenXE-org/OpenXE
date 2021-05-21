<?php

namespace Xentral\Core\ErrorHandler;

use JsonSerializable;
use Throwable;

final class ErrorPageData implements JsonSerializable
{
    /** @var Throwable $exception */
    private $exception;

    /** @var string $title */
    private $title;

    /**
     * @param Throwable   $exception
     * @param string|null $title
     */
    public function __construct($exception, $title = null)
    {
        $this->exception = $exception;
        $this->title = !empty($title) ? (string)$title : 'Xentral: Es ist ein unerwarteter Fehler aufgetreten!';

    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            'information' => $this->prepareSystemInformation(),
            'exception'   => $this->prepareExceptionStack($this->exception),
        ];
    }

    /**
     * @return Throwable
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->getData();
    }

    /**
     * @param Throwable $exception
     *
     * @return array
     */
    private function prepareExceptionStack($exception)
    {
        $stack = [];
        $traces = $exception->getTrace();
        foreach ($traces as $index => $trace) {
            $stack[$index] = $trace;
            unset($stack[$index]['args']);
        }

        return [
            'message'  => $exception->getMessage(),
            'class'    => get_class($exception),
            'code'     => $exception->getCode(),
            'file'     => $exception->getFile(),
            'line'     => $exception->getLine(),
            'trace'    => $stack,
            'previous' => $exception->getPrevious() !== null ? $this->prepareExceptionStack($exception->getPrevious()) : null,
        ];
    }

    /**
     * @return array
     */
    private function prepareSystemInformation()
    {
        return [
            'php'      => [
                'general'    => $this->getPhpGeneralInformations(),
                'settings'   => $this->getPhpImportantSettings(),
                'extensions' => $this->getPhpExtensions(),
            ],
            'software' => $this->getSoftwareInformations(),
            'env'      => $this->getEnvironmentInformation(),
            'server'   => $this->getServerInformation(),
            'request'  => $this->getRequestInformation(),
        ];
    }

    /**
     * @return array
     */
    private function getEnvironmentInformation()
    {
        $scriptFile = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : __FILE__;
        try {
            $fileOwner = !function_exists('posix_getpwuid')?null:@posix_getpwuid(@fileowner($scriptFile));
        } catch (Throwable $e) {
            $fileOwner = null;
        }
        try {
            $fileGroup = !function_exists('posix_getgrgid')?null:@posix_getgrgid(@filegroup($scriptFile));
        }
        catch (Throwable $e) {
            $fileGroup = null;
        }

        return [
            'username'        => !empty(@getenv('USER')) ? @getenv('USER') : @getenv('USERNAME'),
            'home_dir'        => @getenv('HOME'),
            'document_root'   => isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : null,
            'script_filename' => isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : null,
            'script_owner'    => isset($fileOwner['name']) ? $fileOwner['name'] : null,
            'script_group'    => isset($fileGroup['name']) ? $fileGroup['name'] : null,
        ];
    }

    /**
     * @return array
     */
    private function getServerInformation()
    {
        return [
            'software'  => isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : null,
            'signature' => isset($_SERVER['SERVER_SIGNATURE']) ? strip_tags(trim($_SERVER['SERVER_SIGNATURE'])) : null,
            'addr'      => isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : null,
            'name'      => isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : null,
            'port'      => isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : null,
        ];
    }

    /**
     * @return array
     */
    private function getRequestInformation()
    {
        return [
            'is_https'   => $this->isHttpsRequest(),
            'is_ajax'    => $this->isAjaxRequest(),
            'time'       => isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : null,
            'method'     => isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null,
            'scheme'     => isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : null,
            'uri'        => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null,
            'referer'    => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null,
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null,
        ];
    }

    /**
     * @return array
     */
    private function getPhpGeneralInformations()
    {
        return [
            'version'         => PHP_VERSION,
            'version_id'      => PHP_VERSION_ID,
            'version_major'   => PHP_MAJOR_VERSION,
            'version_minor'   => PHP_MINOR_VERSION,
            'version_release' => PHP_RELEASE_VERSION,
            'server_api'      => PHP_SAPI,
            'binary_dir'      => PHP_BINDIR,
            'php_ini_dir'     => php_ini_loaded_file(),
        ];
    }

    /**
     * @return array
     */
    private function getSoftwareInformations()
    {
        $version = '';
        $version_revision = '';
        $versionFile = dirname(dirname(dirname(__DIR__))) . '/version.php';
        if (is_file($versionFile)) {
            include $versionFile;
        }

        /** @var string $version Defined in version.php */
        /** @var string $version_revision Defined in version.php */
        return [
            'xentral_version'  => !empty($version) ? $version : null,
            'xentral_revision' => !empty($version_revision) ? $version_revision : null,
            'fpdf_version'     => $this->getFpdfVersion(),
        ];
    }

    /**
     * @return array
     */
    private function getPhpImportantSettings()
    {
        return [
            [
                'setting'   => 'max_execution_time',
                'raw_value' => $this->getPhpMaxExecutionTimeValue(),
                'int_value' => (int)$this->getPhpMaxExecutionTimeValue(),
            ],
            [
                'setting'   => 'max_input_time',
                'raw_value' => @ini_get('max_input_time'),
                'int_value' => (int)@ini_get('max_input_time'),
            ],
            [
                'setting'   => 'post_max_size',
                'raw_value' => $this->getPostMaxSizeValue(),
                'int_value' => (int)$this->convertPhpValueToBytes($this->getPostMaxSizeValue()),
            ],
            [
                'setting'   => 'upload_max_filesize',
                'raw_value' => @ini_get('upload_max_filesize'),
                'int_value' => (int)$this->convertPhpValueToBytes(@ini_get('upload_max_filesize')),
            ],
            [
                'setting'   => 'memory_limit',
                'raw_value' => @ini_get('memory_limit'),
                'int_value' => (int)$this->convertPhpValueToBytes(@ini_get('memory_limit')),
            ],
        ];
    }

    private function getPhpExtensions()
    {
        $extensionsLoaded = $this->getPhpExtensionsLoaded();
        $extensionsDefined = $this->getPhpExtensionsDefined();
        $extensionsOther = array_diff_key($extensionsLoaded, $extensionsDefined);
        ksort($extensionsOther);

        return [
            'defined' => $this->getPhpExtensionsDefined(),
            'other'   => $extensionsOther,
        ];
    }

    private function getPhpExtensionsLoaded()
    {
        $extensions = get_loaded_extensions();

        return array_combine($extensions, array_fill(0, count($extensions), true));

    }

    /**
     * @return array
     */
    private function getPhpExtensionsDefined()
    {
        $extensionsResult = [];
        $extensionsCheck = $this->getPhpExtensionsDefinedCallbacks();
        foreach ($extensionsCheck as $extension => $callback) {
            $checkResult = $callback();
            $extensionsResult[$extension] = $checkResult;
        }

        return $extensionsResult;
    }

    /**
     * @return array
     */
    private function getPhpExtensionsDefinedCallbacks()
    {
        return [
            'mysqli'                      => function () {
                return function_exists('mysqli_connect');
            },
            'mysqlnd'                     => function () {
                return extension_loaded('mysqlnd');
            },
            'PDO'                         => function () {
                return class_exists('\PDO');
            },
            'curl'                        => function () {
                return function_exists('curl_init');
            },
            'xml'                         => function () {
                return function_exists('simplexml_load_string');
            },
            'stream_socket_enable_crypto' => function () {
                return function_exists('stream_socket_enable_crypto');
            },
            'fsocket'                     => function () {
                return function_exists('fsockopen');
            },
            'openssl'                     => function () {
                return function_exists('openssl_error_string');
            },
            'mbstring'                    => function () {
                return function_exists('mb_encode_numericentity');
            },
            'json'                        => function () {
                return function_exists('json_encode');
            },
            'iconv'                       => function () {
                return function_exists('iconv');
            },
            'soap'                        => function () {
                return class_exists('\SoapClient');
            },
            'imap'                        => function () {
                return function_exists('imap_open');
            },
            'zip'                         => function () {
                return class_exists('\ZipArchive');
            },
            'gd'                          => function () {
                return function_exists('imagejpeg');
            },
            'ldap'                        => function () {
                return function_exists('ldap_connect');
            },
            'ioncube'                     => function () {
                if (!function_exists('ioncube_loader_version')) {
                    return false;
                }

                $ioncubeMajorVersion = (int)@ioncube_loader_version();

                return $ioncubeMajorVersion >= 5;
            },
        ];
    }

    /**
     * @return bool
     */
    private function isHttpsRequest()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            return true;
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') {
            return true;
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * @return string
     */
    private function getPhpMaxExecutionTimeValue()
    {
        $maxExecutionTime = @ini_get('fastcgi_read_timeout'); // Nginx
        if (empty($maxExecutionTime)) {
            $maxExecutionTime = @ini_get('max_execution_time');
        }

        return $maxExecutionTime;
    }

    /**
     * @return string
     */
    private function getPostMaxSizeValue()
    {
        $postMaxSize = @ini_get('client_max_body_size');
        if (empty($postMaxSize)) {
            $postMaxSize = @ini_get('post_max_size');
        }

        return $postMaxSize;
    }

    /**
     * @return int
     */
    private function getFpdfVersion()
    {
        if (defined('USEFPDF3') && (bool)USEFPDF3 === true) {
            return 3;
        }
        if (defined('USEFPDF2') && (bool)USEFPDF2 === true) {
            return 2;
        }

        return 1;
    }

    /**
     * Converts PHP size value to byte value; e.g. 64K => 65536 Bytes
     *
     * @param string $phpValue
     *
     * @return int
     */
    private function convertPhpValueToBytes($phpValue)
    {
        $lastChar = strtoupper(substr(trim($phpValue), -1));
        switch ($lastChar) {
            case 'G':
                $bytes = (int)$phpValue * 1024 * 1024 * 1024;
                break;
            case 'M':
                $bytes = (int)$phpValue * 1024 * 1024;
                break;
            case 'K':
                $bytes = (int)$phpValue * 1024;
                break;
            default:
                $bytes = (int)$phpValue;
        }

        return $bytes;
    }
}
