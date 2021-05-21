<?php

declare(strict_types=1);

namespace Xentral\Components\HttpClient;

use GuzzleHttp\RequestOptions as GuzzleOptionKeys;
use Iterator;
use Xentral\Components\HttpClient\Exception\InvalidArgumentException;
use Xentral\Components\HttpClient\Exception\InvalidRequestOptionsException;
use Xentral\Components\HttpClient\Stream\StreamInterface;

final class RequestOptions
{
    /** @var array $options */
    private $options;

    /**
     * @internal Use setters and public methods instead
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->getDefaultOptions(), $options);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->options;
    }

    /**
     * Sets or overwrites all headers for the current client
     *
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#headers
     *
     * @param array|null $headers
     *
     * @return self
     */
    public function setHeaders(array $headers = null): self
    {
        $this->options[GuzzleOptionKeys::HEADERS] = $headers;

        return $this;
    }

    /**
     * Sets or overwrites a specific header
     *
     * Headers added here are defaults for the created client. Headers can be overwritten for single requests.
     *
     * @example setHeader('Accept', 'text/html')
     * @example setHeader('Accept', ['text/html', 'text'/plain'])
     *
     * @param string      $headerType
     * @param string|string[]|null ...$headerValue `null` to remove a header
     *
     * @return self
     */
    public function setHeader(string $headerType, ...$headerValue): self
    {
        if ($headerValue === null) {
            unset($this->options[GuzzleOptionKeys::HEADERS][$headerType]);
        } else {
            $this->options[GuzzleOptionKeys::HEADERS][$headerType] = $headerValue;
        }

        return $this;
    }

    /**
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#expect
     *
     * @return self
     */
    public function enableExpectHeader(): self
    {
        $this->options[GuzzleOptionKeys::EXPECT] = true;

        return $this;
    }

    /**
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#expect
     *
     * @return self
     */
    public function disableExpectHeader(): self
    {
        $this->options[GuzzleOptionKeys::EXPECT] = false;

        return $this;
    }

    /**
     * Sets the body of the request
     *
     * @param resource|string|null|int|float|StreamInterface|callable|Iterator $body
     *
     * @return self
     */
    public function setBody($body): self
    {
        $this->options[GuzzleOptionKeys::BODY] = $body;

        return $this;
    }

    /**
     * Used to send an application/x-www-form-urlencoded POST request
     *
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#form-params
     *
     * @param array $formParams
     *
     * @throws InvalidRequestOptionsException
     *
     * @return self
     */
    public function setBodyFromFormParams(array $formParams): self
    {
        if (isset($this->options[GuzzleOptionKeys::BODY])) {
            throw new InvalidRequestOptionsException('Form params body can not be set. Body is already set.');
        }
        if (isset($this->options[GuzzleOptionKeys::MULTIPART])) {
            throw new InvalidRequestOptionsException('Form params body can not be set. Multipart body is already set.');
        }

        $this->options[GuzzleOptionKeys::FORM_PARAMS] = $formParams;

        return $this;
    }

    /**
     * Used to send an multipart/form-data requests
     *
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#multipart
     *
     * @param array $multipartParams
     *
     * @throws InvalidRequestOptionsException
     *
     * @return self
     */
    public function setBodyFromMultipartParams(array $multipartParams): self
    {
        if (isset($this->options[GuzzleOptionKeys::BODY])) {
            throw new InvalidRequestOptionsException('Multipart body can not be set. Body is already set.');
        }
        if (isset($this->options[GuzzleOptionKeys::FORM_PARAMS])) {
            throw new InvalidRequestOptionsException('Multipart body can not be set. Form params body is already set.');
        }

        $this->options[GuzzleOptionKeys::MULTIPART] = $multipartParams;

        return $this;
    }

    /**
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#auth
     *
     * @param string $username
     * @param string $password
     *
     * @return self
     */
    public function setAuthBasic(string $username, string $password): self
    {
        $this->options[GuzzleOptionKeys::AUTH] = [$username, $password];

        return $this;
    }

    /**
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#auth
     *
     * @param string $username
     * @param string $password
     *
     * @return self
     */
    public function setAuthDigest(string $username, string $password): self
    {
        $this->options[GuzzleOptionKeys::AUTH] = [$username, $password, 'digest'];

        return $this;
    }

    /**
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#auth
     *
     * @param string $username
     * @param string $password
     *
     * @return self
     */
    public function setAuthNtlm(string $username, string $password): self
    {
        $this->options[GuzzleOptionKeys::AUTH] = [$username, $password, 'ntlm'];

        return $this;
    }

    /**
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#auth
     *
     * @param array|string|null $value
     *
     * @return self
     */
    public function setAuthCustom($value): self
    {
        $this->options[GuzzleOptionKeys::AUTH] = $value;

        return $this;
    }

    /**
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#debug
     *
     * @param resource $resource
     *
     * @throws InvalidArgumentException
     *
     * @return self
     */
    public function setDebugResource($resource): self
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('Debug resource is not a valid resource.');
        }

        $this->options[GuzzleOptionKeys::DEBUG] = $resource;

        return $this;
    }

    /**
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#version
     *
     * @param float|string $version Default '1.1'
     *
     * @return self
     */
    public function setProtocolVersion($version): self
    {
        $this->options[GuzzleOptionKeys::VERSION] = $version;

        return $this;
    }

    /**
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#allow-redirects
     *
     * @return self
     */
    public function allowRedirects(): self
    {
        $this->options[GuzzleOptionKeys::ALLOW_REDIRECTS] = $this->getDefaultRedirectOptions();

        return $this;
    }

    /**
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#allow-redirects
     *
     * @return self
     */
    public function disallowRedirects(): self
    {
        $this->options[GuzzleOptionKeys::ALLOW_REDIRECTS] = false;

        return $this;
    }

    /**
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#allow-redirects
     *
     * @param int $redirectCount
     *
     * @return self
     */
    public function setMaxRedirectsCount(int $redirectCount): self
    {
        $redirectOptions = $this->getDefaultRedirectOptions();

        if ($redirectCount <= 0) {
            $redirectOptions = false;
        }
        if ($redirectCount > 0) {
            $redirectOptions['max'] = $redirectCount;
        }

        $this->options[GuzzleOptionKeys::ALLOW_REDIRECTS] = $redirectOptions;

        return $this;
    }

    /**
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#sink
     *
     * @param string|resource $location
     *
     * @return self
     */
    public function setStorageLocation($location): self
    {
        $this->options[GuzzleOptionKeys::SINK] = $location;

        return $this;
    }

    /**
     * Attempt to stream a response rather than download it all up-front.
     *
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#stream
     *
     * @return self
     */
    public function enableStream(): self
    {
        $this->options[GuzzleOptionKeys::STREAM] = true;

        return $this;
    }

    /**
     * (Default behavior)
     *
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#stream
     *
     * @return self
     */
    public function disableStream(): self
    {
        $this->options[GuzzleOptionKeys::STREAM] = false;

        return $this;
    }

    /**
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#ssl-key
     *
     * @param string      $path
     * @param string|null $passphrase
     *
     * @return self
     */
    public function setSslKey(string $path, string $passphrase = null): self
    {
        if ($passphrase !== null) {
            $this->options[GuzzleOptionKeys::SSL_KEY] = [$path, $passphrase];
        } else {
            $this->options[GuzzleOptionKeys::SSL_KEY] = $path;
        }

        return $this;
    }

    /**
     * Default behaviour
     *
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#verify
     *
     * @return self
     */
    public function enableSslVerification(): self
    {
        $this->options[GuzzleOptionKeys::VERIFY] = true;

        return $this;
    }

    /**
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#verify
     *
     * @return self
     */
    public function disableSslVerification(): self
    {
        $this->options[GuzzleOptionKeys::VERIFY] = false;

        return $this;
    }

    /**
     * Enable SSL verification using a custom certificate
     *
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#verify
     *
     * @param string $certificatePath Path to SSL certificate on disk
     *
     * @return self
     */
    public function setCustomSslVerification(string $certificatePath): self
    {
        $this->options[GuzzleOptionKeys::VERIFY] = $certificatePath;

        return $this;
    }

    /**
     * Sets the timeout of the request in seconds. Use 0 to wait indefinitely (default behavior).
     *
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#timeout
     *
     * @param float $seconds
     *
     * @return self
     */
    public function setTimeout(float $seconds = 0.0): self
    {
        $this->options[GuzzleOptionKeys::TIMEOUT] = $seconds;

        return $this;
    }

    /**
     * Disables exceptions on HTTP protocol errors (4xx and 5xx status)
     *
     * By default, exceptions will be thrown on HTTP protocol errors
     *
     * @see http://docs.guzzlephp.org/en/6.5/request-options.html#http_errors
     *
     * @return self
     */
    public function disableHttpErrorExceptions(): self
    {
        $this->options[GuzzleOptionKeys::HTTP_ERRORS] = false;

        return $this;
    }

    /**
     * @return array
     */
    private function getDefaultOptions(): array
    {
        return [
            GuzzleOptionKeys::ALLOW_REDIRECTS => $this->getDefaultRedirectOptions(),
            GuzzleOptionKeys::DEBUG           => false,
            GuzzleOptionKeys::TIMEOUT         => 0,
            GuzzleOptionKeys::VERIFY          => true,
            GuzzleOptionKeys::VERSION         => 1.1,
            GuzzleOptionKeys::STREAM          => false,
        ];
    }

    /**
     * @return array
     */
    private function getDefaultRedirectOptions(): array
    {
        return [
            'max'             => 5,
            'strict'          => false,
            'referer'         => false,
            'protocols'       => ['http', 'https'],
            'track_redirects' => false,
        ];
    }
}
