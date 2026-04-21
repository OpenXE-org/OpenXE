<?php
/**
 * Thin wrapper around the official automattic/woocommerce Composer SDK client.
 *
 * Adds the two accessors that the OpenXE business logic requires but that the
 * upstream SDK does not expose:
 *
 *   - getLastResponse(): ResponseWrapper   last HTTP response after any API call
 *   - ResponseWrapper::getHeader($name)    case-insensitive single-header lookup
 *
 * Also handles the OpenXE-specific ssl_ignore flag and passes it through to the
 * upstream client via the verify_ssl option.
 *
 * @package Xentral\Components\WooCommerce
 */

namespace Xentral\Components\WooCommerce;

use Automattic\WooCommerce\Client as UpstreamClient;
use Automattic\WooCommerce\HttpClient\HttpClientException;

/**
 * Wraps a single upstream Response and exposes a case-insensitive getHeader().
 */
class ResponseWrapper
{
    /** @var \Automattic\WooCommerce\HttpClient\Response */
    private $response;

    /** @var array<string,string> lowercase-keyed header map */
    private $lowercaseHeaders;

    public function __construct(\Automattic\WooCommerce\HttpClient\Response $response)
    {
        $this->response = $response;

        // Build a normalised, lowercase header map once.
        $this->lowercaseHeaders = [];
        foreach ($response->getHeaders() as $key => $value) {
            $this->lowercaseHeaders[strtolower($key)] = $value;
        }
    }

    /**
     * Case-insensitive single-header lookup.
     *
     * @param string $name Header name (e.g. 'x-wp-total', 'X-WP-TotalPages')
     * @return string|null Header value, or null when not present
     */
    public function getHeader($name)
    {
        $key = strtolower($name);
        return isset($this->lowercaseHeaders[$key]) ? $this->lowercaseHeaders[$key] : null;
    }

    /**
     * All response headers (lowercase keys).
     *
     * @return array<string,string>
     */
    public function getHeaders()
    {
        return $this->lowercaseHeaders;
    }

    /** @return int HTTP status code */
    public function getCode()
    {
        return $this->response->getCode();
    }

    /** @return string Response body */
    public function getBody()
    {
        return $this->response->getBody();
    }
}

/**
 * Drop-in replacement for the old inline WCClient.
 *
 * Wraps the upstream Automattic\WooCommerce\Client and adds:
 *   - getLastResponse(): ResponseWrapper
 *   - ssl_ignore support via verify_ssl option
 *   - PSR-3-style logger integration: HTTP >= 400 is logged as warning,
 *     HttpClientException is logged as error (and rethrown)
 */
class ClientWrapper
{
    /** @var UpstreamClient */
    private $client;

    /** @var ResponseWrapper|null */
    private $lastResponse;

    /** @var \Psr\Log\LoggerInterface|null */
    private $logger;

    /**
     * @param string     $url            WooCommerce store URL
     * @param string     $consumerKey    API consumer key
     * @param string     $consumerSecret API consumer secret
     * @param array      $options        Upstream SDK options (version, timeout, …)
     * @param mixed      $logger         PSR-3-compatible logger; null disables logging
     * @param bool|mixed $sslIgnore      When truthy, disables SSL certificate verification
     */
    public function __construct($url, $consumerKey, $consumerSecret, $options = [], $logger = null, $sslIgnore = false)
    {
        if ($sslIgnore) {
            $options['verify_ssl'] = false;
        }

        $this->client = new UpstreamClient($url, $consumerKey, $consumerSecret, $options);
        $this->logger = $logger;
    }

    /**
     * GET request.
     *
     * @param string $endpoint   API endpoint
     * @param array  $parameters Query parameters
     * @return \stdClass|array
     * @throws HttpClientException
     */
    public function get($endpoint, $parameters = [])
    {
        return $this->dispatch('GET', $endpoint, function () use ($endpoint, $parameters) {
            return $this->client->get($endpoint, $parameters);
        });
    }

    /**
     * POST request.
     *
     * @param string $endpoint API endpoint
     * @param array  $data     Request body
     * @return \stdClass
     * @throws HttpClientException
     */
    public function post($endpoint, $data)
    {
        return $this->dispatch('POST', $endpoint, function () use ($endpoint, $data) {
            return $this->client->post($endpoint, $data);
        });
    }

    /**
     * PUT request.
     *
     * @param string $endpoint API endpoint
     * @param array  $data     Request body
     * @return \stdClass
     * @throws HttpClientException
     */
    public function put($endpoint, $data)
    {
        return $this->dispatch('PUT', $endpoint, function () use ($endpoint, $data) {
            return $this->client->put($endpoint, $data);
        });
    }

    /**
     * DELETE request.
     *
     * @param string $endpoint   API endpoint
     * @param array  $parameters Query parameters
     * @return \stdClass
     * @throws HttpClientException
     */
    public function delete($endpoint, $parameters = [])
    {
        return $this->dispatch('DELETE', $endpoint, function () use ($endpoint, $parameters) {
            return $this->client->delete($endpoint, $parameters);
        });
    }

    /**
     * OPTIONS request.
     *
     * @param string $endpoint API endpoint
     * @return \stdClass
     * @throws HttpClientException
     */
    public function options($endpoint)
    {
        return $this->dispatch('OPTIONS', $endpoint, function () use ($endpoint) {
            return $this->client->options($endpoint);
        });
    }

    /**
     * Returns the response from the most recent API call, or null when no
     * request has been made yet.
     *
     * @return ResponseWrapper|null
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Run an upstream call with unified response capture and error logging.
     * Exceptions are logged (when a logger is available) and rethrown so
     * existing callsites keep their try/catch contract.
     *
     * @param string   $method   HTTP verb for log context
     * @param string   $endpoint API endpoint for log context
     * @param callable $call     Zero-argument closure performing the upstream call
     * @return mixed Upstream result
     * @throws HttpClientException
     */
    private function dispatch($method, $endpoint, callable $call)
    {
        try {
            $result = $call();
        } catch (HttpClientException $e) {
            $this->captureResponse($method, $endpoint);
            if ($this->logger !== null) {
                $this->logger->error(
                    sprintf('WooCommerce %s %s failed: %s', $method, $endpoint, $e->getMessage()),
                    ['code' => $e->getCode()]
                );
            }
            throw $e;
        }
        $this->captureResponse($method, $endpoint);
        return $result;
    }

    /**
     * Snapshot the upstream response after each request. HTTP >= 400 is
     * logged as warning with a truncated body excerpt to keep logs small.
     *
     * @param string $method   HTTP verb for log context
     * @param string $endpoint API endpoint for log context
     */
    private function captureResponse($method, $endpoint)
    {
        $upstreamResponse = $this->client->http->getResponse();
        if ($upstreamResponse === null) {
            return;
        }
        $this->lastResponse = new ResponseWrapper($upstreamResponse);

        if ($this->logger !== null) {
            $code = $upstreamResponse->getCode();
            if ($code >= 400) {
                $this->logger->warning(
                    sprintf('WooCommerce %s %s returned HTTP %d', $method, $endpoint, $code),
                    ['body' => substr((string) $upstreamResponse->getBody(), 0, 500)]
                );
            }
        }
    }
}
