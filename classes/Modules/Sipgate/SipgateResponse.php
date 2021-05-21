<?php

namespace Xentral\Modules\Sipgate;

use Xentral\Modules\Sipgate\Exception\ResponseException;
use Xentral\Modules\Sipgate\Exception\ResponseDecodeException;

class SipgateResponse
{
    /** @var string $result The response body as string. */
    private $result = '';

    /** @var array $body The decoded body as array. */
    private $body = [];

    /** @var array $info The curl info via curl_getinfo() */
    private $info = [];

    /**
     * @param string $result
     * @param array  $info
     *
     * @throws ResponseDecodeException
     */
    public function __construct($result, $info = [])
    {
        $this->result = $result;
        $this->info = $info;

        $this->body = $this->decode($result);
    }

    /**
     * Just a helper method to print out some attributes.
     *
     * @deprecated
     *
     * @param bool $info
     *
     * @return void
     */
    public function dump($info = true)
    {
        echo '<pre style="border: 5px solid #333;padding: 1em;float: left">';
        echo PHP_EOL;
        var_dump($this->body);
        echo PHP_EOL;
        if ($info) {
            var_dump($this->info);
            echo PHP_EOL;
        }
        echo '</pre>';
    }

    /**
     * @return string
     */
    public function getPlainResult()
    {
        return $this->result;
    }

    /**
     * @return array
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $key
     * @param mixed  $fallback
     *
     * @return mixed
     */
    public function getInfo($key, $fallback = null)
    {
        return array_key_exists($key, $this->info)
            ? $this->info[$key]
            : $fallback;
    }

    /**
     * Get the status code
     *
     * @deprecated
     *
     * @return int
     */
    final public function getStatus()
    {
        return $this->getStatusCode();
    }

    /**
     * Get the status code
     *
     * @return int
     */
    final public function getStatusCode()
    {
        $status = $this->getInfo('http_code');

        return (int)$status;
    }

    /**
     * Get the final url
     *
     * @return string
     */
    final public function getURL()
    {
        $url = $this->getInfo('url');

        return (string)$url;
    }

    /**
     * Decode the result body.
     *
     * @param string $contents
     *
     * @throws ResponseDecodeException
     *
     * @return array
     */
    private function decode($contents)
    {
        if (!array_key_exists('content_type', $this->info)) {
            return [];
        }
        $type = $this->info['content_type'];
        switch ($type) {
            case 'application/json':
                $result = json_decode($contents, true);
                $errno = json_last_error();
                if ($errno) {
                    $msg = json_last_error_msg();
                    throw new ResponseDecodeException(sprintf(
                        'JSON decode error (Code %s): %s',
                        $errno,
                        $msg
                    ));
                }

                if (array_key_exists('ERROR', $result)) {
                    $status = $this->info['http_code'];
                    $error = $result['ERROR'];

                    // @todo Exception Message überdenken; Was passiert genau?
                    throw new ResponseException("Error ({$status}): {$error}");
                }

                break;
            default:
                $result = [];
        }

        return $result;
    }
}
