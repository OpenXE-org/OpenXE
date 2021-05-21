<?php

namespace Xentral\Modules\Api\Http;

use RuntimeException;

/**
 * @deprecated Use Xentral\Components\Http instead
 */
class Response
{
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_INTERNAL_SERVER_ERROR = 500;

    /** @var array $statusMessages */
    protected $statusMessages = [
        self::HTTP_OK => 'OK',
        self::HTTP_CREATED => 'Created',
        self::HTTP_BAD_REQUEST => 'Bad Request',
        self::HTTP_UNAUTHORIZED => 'Unauthorized',
        self::HTTP_FORBIDDEN => 'Forbidden',
        self::HTTP_NOT_FOUND => 'Not Found',
        self::HTTP_METHOD_NOT_ALLOWED => 'Method Not Allowed',
        self::HTTP_INTERNAL_SERVER_ERROR => 'Internal Server Error',
    ];

    /** @var array $headers */
    protected $headers = [];

    /** @var string $content Response-Content */
    protected $content;

    /** @var int $statusCode HTTP-Statuscode */
    protected $statusCode;

    /** @var string $statusText HTTP-Statustext */
    protected $statusText;

    /** @var string $protocolVersion */
    protected $protocolVersion = '1.1';

    /**
     * @param string $content
     * @param int    $statusCode
     * @param array  $headers
     */
    public function __construct($content, $statusCode, array $headers = [])
    {
        $this->content = $content;
        $this->headers = $headers;
        $this->statusCode = $statusCode;
    }

    /**
     * Response an Client senden
     */
    public function send()
    {
        header(sprintf('HTTP/%s %s %s', $this->protocolVersion, $this->statusCode, $this->statusText));

        foreach ($this->headers as $name => $value) {
            header(sprintf('%s: %s', $name, $value), false, $this->statusCode);
        }

        echo $this->content;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = (string)$content;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        if (!array_key_exists($statusCode, $this->statusMessages)) {
            throw new RuntimeException(sprintf('Status Code %s is not supported', $statusCode));
        }

        $this->statusCode = $statusCode;
    }

    /**
     * @return string HTTP-Statustext
     */
    public function getStatusText()
    {
        if ($this->statusText === null) {
            $this->statusText = $this->statusMessages[$this->statusCode];
        }

        return $this->statusText;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getHeader($name)
    {
        return $this->headers[$name];
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
