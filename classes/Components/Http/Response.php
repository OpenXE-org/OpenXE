<?php

namespace Xentral\Components\Http;

use DateTimeInterface;
use Xentral\Components\Http\Cookie\Cookie;
use Xentral\Components\Http\Cookie\CookieCollection;
use Xentral\Components\Http\Exception\HttpHeaderValueException;
use Xentral\Components\Http\Exception\InvalidArgumentException;
use Xentral\Components\Util\StringUtil;

class Response
{
    const HTTP_CONTINUE = 100;
    const HTTP_SWITCHING_PROTOCOLS = 101;
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPT = 202;
    const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    const HTTP_NO_CONTENT = 204;
    const HTTP_RESET_CONTENT = 205;
    const HTTP_PARTIAL_CONTENT = 206;
    const HTTP_MULTIPLE_CHOICES = 300;
    const HTTP_MOVED_PERMANENTLY = 301;
    const HTTP_MOVED_TEMPORARILY = 302;
    const HTTP_SEE_OTHER = 303;
    const HTTP_NOT_MODIFIED = 304;
    const HTTP_USE_PROXY = 305;
    const HTTP_TEMPORARY_REDIRECT = 307;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_PAYMENT_REQUIRED = 402;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    const HTTP_REQUEST_TIMEOUT = 408;
    const HTTP_CONFILICT = 409;
    const HTTP_GONE = 410;
    const HTTP_LENGTH_REQUIRED = 411;
    const HTTP_PRECONDITION_FAILED = 412;
    const HTTP_PAYLOAD_TOO_LARGE = 413;
    const HTTP_URI_TOO_LONG = 414;
    const HTTP_UNSUPPORTET_MEDIA_TYPE = 415;
    const HTTP_RANGE_NOT_SATISFIABLE = 416;
    const HTTP_EXPECTATION_FAILED = 417;
    const HTTP_UNPROCESSABLE_ENTITY = 422;
    const HTTP_UPGRADE_REQUIRED = 426;
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;
    const HTTP_BAD_GATEWAY = 502;
    const HTTP_SERVICE_UNAVAILABLE = 503;
    const HTTP_GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    const DISPOSITION_INLINE = 'inline';
    const DISPOSITION_ATTACHMENT = 'attachment';

    /** @var array $statusMessages */
    protected static $statusMessages = [
        self::HTTP_CONTINUE                      => 'Continue',
        self::HTTP_SWITCHING_PROTOCOLS           => 'Switching Protocols',
        self::HTTP_NON_AUTHORITATIVE_INFORMATION => 'Non-Authoritative Information',
        self::HTTP_OK                            => 'OK',
        self::HTTP_CREATED                       => 'Created',
        self::HTTP_ACCEPT                        => 'Accepted',
        self::HTTP_NO_CONTENT                    => 'No Content',
        self::HTTP_RESET_CONTENT                 => 'Reset Content',
        self::HTTP_PARTIAL_CONTENT               => 'Partial Content',
        self::HTTP_MULTIPLE_CHOICES              => 'Multiple Choices',
        self::HTTP_MOVED_PERMANENTLY             => 'Moved Permanently',
        self::HTTP_MOVED_TEMPORARILY             => 'Found',
        self::HTTP_SEE_OTHER                     => 'See Other',
        self::HTTP_NOT_MODIFIED                  => 'Not Modified',
        self::HTTP_USE_PROXY                     => 'Use Proxy',
        self::HTTP_TEMPORARY_REDIRECT            => 'Temporary Redirect',
        self::HTTP_BAD_REQUEST                   => 'Bad Request',
        self::HTTP_UNAUTHORIZED                  => 'Unauthorized',
        self::HTTP_PAYMENT_REQUIRED              => 'Payment Required',
        self::HTTP_FORBIDDEN                     => 'Forbidden',
        self::HTTP_NOT_FOUND                     => 'Not Found',
        self::HTTP_METHOD_NOT_ALLOWED            => 'Method Not Allowed',
        self::HTTP_NOT_ACCEPTABLE                => 'Not Acceptable',
        self::HTTP_PROXY_AUTHENTICATION_REQUIRED => 'Proxy Authentication Required',
        self::HTTP_REQUEST_TIMEOUT               => 'Request Timeout',
        self::HTTP_CONFILICT                     => 'Conflict',
        self::HTTP_GONE                          => 'Gone',
        self::HTTP_LENGTH_REQUIRED               => 'Length Required',
        self::HTTP_PRECONDITION_FAILED           => 'Precondition Failed',
        self::HTTP_PAYLOAD_TOO_LARGE             => 'Payload Too Large',
        self::HTTP_URI_TOO_LONG                  => 'URI Too Long',
        self::HTTP_UNSUPPORTET_MEDIA_TYPE        => 'Unsupported Media Type',
        self::HTTP_RANGE_NOT_SATISFIABLE         => 'Range Not Satisfiable',
        self::HTTP_EXPECTATION_FAILED            => 'Expectation Failed',
        self::HTTP_UPGRADE_REQUIRED              => 'Upgrade Required',
        self::HTTP_INTERNAL_SERVER_ERROR         => 'Internal Server Error',
        self::HTTP_NOT_IMPLEMENTED               => 'Not Implemented',
        self::HTTP_BAD_GATEWAY                   => 'Bad Gateway',
        self::HTTP_SERVICE_UNAVAILABLE           => 'Service Unavailable',
        self::HTTP_GATEWAY_TIMEOUT               => 'Gateway Timeout',
        self::HTTP_VERSION_NOT_SUPPORTED         => 'HTTP Version Not Supported',
    ];

    /** @var array $oneValueHeaders headers which can hold ONE value ONLY */
    protected static $oneValueHeaders = ['content-type', 'content-length', 'content-disposition', 'date'];

    /** @var array $headers */
    protected $headers = [];

    /** @var array $headerNames */
    protected $headerNames = [];

    /** @var string $content Response content */
    protected $content;

    /** @var int $statusCode HTTP status code */
    protected $statusCode;

    /** @var string $statusText HTTP status text */
    protected $statusText;

    /** @var string $protocolVersion HTTP protocol version */
    protected $protocolVersion;

    /** @var CookieCollection $cookies */
    protected $cookies;

    /**
     * @param string                 $content
     * @param int                    $statusCode
     * @param array                  $headers
     * @param string                 $protocolVersion
     * @param null                   $statusText
     * @param CookieCollection|array $cookies
     */
    public function __construct(
        $content = null,
        $statusCode = self::HTTP_OK,
        array $headers = [],
        $protocolVersion = '1.1',
        $statusText = null,
        $cookies = []
    ) {
        $this->statusCode = (int)$statusCode;
        $this->headers = [];
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }
        if (!$this->hasHeader('content-type')) {
            $this->setHeader('Content-Type', 'text/html; charset=utf-8');
        }
        $this->setContent($content);
        $this->protocolVersion = $protocolVersion;
        $this->statusText = $statusText;
        $this->setCookies($cookies);
    }

    /**
     * Sends the response headers and content to the client.
     *
     * @param DateTimeInterface|null $sendTime
     *
     * @return void
     */
    public function send(DateTimeInterface $sendTime = null)
    {
        header(sprintf('HTTP/%s %s %s', $this->getProtocolVersion(), $this->getStatusCode(), $this->getStatusText()));

        if (!$this->hasHeader('date')) {
            if ($sendTime === null) {
                $time = time();
            } else {
                $time = $sendTime->getTimestamp();
            }
            $this->setHeader('Date', gmdate('D, d M Y H:i:s \G\M\T', $time));
        }

        foreach ($this->headers as $name => $value) {
            $replace = strtolower($name) === 'content-type';
            $value = implode(', ', $value);
            header(sprintf('%s: %s', $this->headerNames[$name], $value), $replace, $this->getStatusCode());
        }
        foreach ($this->cookies as $key => $cookie) {
            header($cookie->toHttpHeader(), false);
        }

        if ($this->content !== null) {
            echo $this->content;
        }
    }

    /**
     * Returns the response body.
     *
     * @return string|null
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets the response body.
     *
     * @param string|null $content
     */
    public function setContent($content)
    {
        if ($content !== null) {
            $this->content = (string)$content;
            $this->setHeader('Content-Length', (string)strlen($this->content));
        } else {
            $this->content = null;
            unset($this->headers['content-type'], $this->headers['content-length']);
        }
    }

    /**
     * Returns the HTTP status code.
     *
     * @return int HTTP status code
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Sets the HTTP status code.
     *
     * @param int $statusCode HTTP status code
     */
    public function setStatusCode($statusCode)
    {
        if (!array_key_exists($statusCode, self::$statusMessages)) {
            throw new InvalidArgumentException(sprintf('Status Code %s is not supported.', $statusCode));
        }

        $this->statusCode = (int)$statusCode;
    }

    /**
     * Gets the HTTP status text (=reason).
     *
     * @return string HTTP status text
     */
    public function getStatusText()
    {
        if ($this->statusText === null) {
            $this->statusText = self::$statusMessages[$this->statusCode];
        }

        return $this->statusText;
    }

    /**
     * Sets the HTTP status text (=reason).
     *
     * @param string $message
     */
    public function setStatusText($message)
    {
        $this->statusText = $message;
    }

    /**
     * Returns the HTTP version
     *
     * @return string
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * Sets header (overwrites existing header).
     *
     * @param string          $name
     * @param string|string[] $values
     */
    public function setHeader($name, $values)
    {
        if (!is_string($values) && !is_array($values)) {
            throw new HttpHeaderValueException(
                'Invalid header, only string|string[] allowed',
                0,
                null,
                $values
            );
        }
        if ($values === '' || $values === []) {
            throw new HttpHeaderValueException('Empty header not allowed', 0, null, $values);
        }
        if (!is_array($values)) {
            $values = [$values];
        }
        foreach ($values as $value) {
            $value = $this->sanitizeHeaderValue($value);
            if (!is_string($value) || $value === '') {
                throw new HttpHeaderValueException('Invalid header', 0, null, $value);
            }
        }
        $normalized = $this->normalizeHeaderName($name);
        $this->headers[$normalized] = $values;
        $this->headerNames[$normalized] = $name;
    }

    /**
     * Sets header (appends existing header).
     *
     * @param string $name
     * @param string $value
     */
    public function addHeader($name, $value)
    {
        if (in_array($this->normalizeHeaderName($name), self::$oneValueHeaders, true)) {
            throw new InvalidArgumentException(sprintf('Cannot append header "%s".', $name));
        }
        if (!is_string($value)) {
            throw new HttpHeaderValueException('Invalid header', 0, null, $value);
        }
        $value = $this->sanitizeHeaderValue($value);
        if ($value === '') {
            throw new HttpHeaderValueException('Empty header not allowed', 0, null, $value);
        }

        $normalized = $this->normalizeHeaderName($name);
        $header = $this->getHeader($normalized);
        if (count($header) === 0) {
            $this->headerNames[$normalized] = $name;
        }

        if (count(array_intersect($header, [$value])) === 0) {
            $header[] = $value;
            $this->headers[$normalized] = $header;
        }
    }

    /**
     * Gets header as array.
     *
     * @param string $name
     *
     * @return string[]|array empty if not set
     */
    public function getHeader($name)
    {
        $normalized = $this->normalizeHeaderName($name);
        if ($this->hasHeader($normalized)) {
            return $this->headers[$normalized];
        }

        return [];
    }

    /**
     * Gets header as comma-seperated string.
     *
     * @example getHeaderLine(Headername) -> 'value1, value2'
     *
     * @param string $name
     *
     * @return string|null null if header not set
     */
    public function getHeaderLine($name)
    {
        $header = $this->getHeader($name);
        if (count($header) === 0) {
            return null;
        }

        return implode(', ', $header);
    }

    /**
     * Returns true if the header exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasHeader($name)
    {
        return array_key_exists($this->normalizeHeaderName($name), $this->headers);
    }

    /**
     * Returns all headers
     *
     * @return array all headers
     */
    public function getHeaders()
    {
        $headers = [];
        foreach ($this->headers as $name => $value) {
            $headers[$this->headerNames[$name]] = $value;
        }

        return $headers;
    }

    /**
     * Returns all headers with normalized names
     *
     * @return array all headers
     */
    public function getHeadersNormalized()
    {
        return $this->headers;
    }

    /**
     * Returns the value of the Content-Type header.
     *
     * @example getContentType() -> 'text/html; charset=utf-8'
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->getHeaderLine('content-type');
    }

    /**
     * Overwrites the Content-Type header.
     *
     * @param string $contentType
     * @param string $charset
     */
    public function setContentType($contentType, $charset = 'utf-8')
    {
        $this->setHeader('Content-Type', sprintf('%s; charset=%s', $contentType, $charset));
    }

    /**
     * Returns value of the Content-Disposition header.
     *
     * @example getContentDisposition() -> 'attachment; filename*="file.txt"; filename="file.txt"'
     *
     * @return string Content-Disposition header
     */
    public function getContentDisposition()
    {
        return $this->getHeaderLine('content-disposition');
    }

    /**
     * Sets the Content-Disposition HTTP header.
     *
     * @param string $disposition    values: 'inline'|'attachment'
     * @param string $clientFileName file name for download on client
     */
    public function setContentDisposition($disposition = self::DISPOSITION_ATTACHMENT, $clientFileName = '')
    {
        $disposition = strtolower($disposition);
        if (!in_array($disposition, [self::DISPOSITION_ATTACHMENT, self::DISPOSITION_INLINE], true)) {
            throw new InvalidArgumentException(sprintf('Invalid Content-Disposition "%s".', $disposition));
        }
        if ($clientFileName === '') {
            throw new InvalidArgumentException('Filename required.');
        }

        $encodedName = urlencode(StringUtil::toFilename($clientFileName));
        $fallbackName = StringUtil::toAscii($clientFileName);

        $header = sprintf('%s; filename*="%s"', $disposition, $encodedName);
        if ($fallbackName !== '') {
            $header .= sprintf('; filename="%s"', $fallbackName);
        }
        $this->setHeader('Content-Disposition', $header);
    }

    /**
     * Removes all non-ASCII characters from a header value
     *
     * @param string $value
     *
     * @return string
     */
    protected function sanitizeHeaderValue($value)
    {
        if (!is_string($value)) {
            throw new HttpHeaderValueException('Invalid header', 0, null, $value);
        }

        return StringUtil::toAscii($value);
    }

    /**
     * @param string $name
     * @param string $value
     * @param int    $timeToLive 0 = for ever
     */
    public function addSimpleCookie($name, $value, $timeToLive = 0)
    {
        $this->addCookie(new Cookie($name, $value, $timeToLive));
    }

    /**
     * @param Cookie $cookie
     */
    public function addCookie(Cookie $cookie)
    {
        $this->cookies[] = $cookie;
    }

    /**
     * @param string $cookieName
     */
    public function removeCookie($cookieName)
    {
        foreach ($this->cookies as $key => $cookie) {
            if ($cookieName === $cookie->getName()) {
                unset($this->cookies[$key]);
            }
        }
    }

    /**
     * @return CookieCollection
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @param CookieCollection|array $cookies
     */
    protected function setCookies($cookies)
    {
        if (is_object($cookies) && get_class($cookies) === CookieCollection::class) {
            $this->cookies = $cookies;
        } else {
            $this->cookies = new CookieCollection($cookies);
        }
    }

    /**
     * Transforms header name to normalized form.
     *
     * @example 'HEADER-NAME' -> 'header-name'
     *
     * @param string $name
     *
     * @return string
     */
    protected function normalizeHeaderName($name)
    {
        if (!preg_match('/^[a-zA-Z0-9\-]+$/', $name)) {
            throw new InvalidArgumentException(sprintf('Invalid character in header name "%s".', $name));
        }

        return str_replace('_', '-', strtolower($name));
    }
}
