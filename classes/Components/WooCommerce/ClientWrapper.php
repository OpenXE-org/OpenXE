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
 *   - logger parameter (accepted but unused — upstream has no logging hook)
 */
class ClientWrapper
{
    /** @var UpstreamClient */
    private $client;

    /** @var ResponseWrapper|null */
    private $lastResponse;

    /**
     * @param string     $url            WooCommerce store URL
     * @param string     $consumerKey    API consumer key
     * @param string     $consumerSecret API consumer secret
     * @param array      $options        Upstream SDK options (version, timeout, …)
     * @param mixed      $logger         Accepted for API compatibility; unused
     * @param bool|mixed $sslIgnore      When truthy, disables SSL certificate verification
     */
    public function __construct($url, $consumerKey, $consumerSecret, $options = [], $logger = null, $sslIgnore = false)
    {
        if ($sslIgnore) {
            $options['verify_ssl'] = false;
        }

        $this->client = new UpstreamClient($url, $consumerKey, $consumerSecret, $options);
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
        $result = $this->client->get($endpoint, $parameters);
        $this->captureResponse();
        return $result;
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
        $result = $this->client->post($endpoint, $data);
        $this->captureResponse();
        return $result;
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
        $result = $this->client->put($endpoint, $data);
        $this->captureResponse();
        return $result;
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
        $result = $this->client->delete($endpoint, $parameters);
        $this->captureResponse();
        return $result;
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
        $result = $this->client->options($endpoint);
        $this->captureResponse();
        return $result;
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
     * Snapshot the upstream response after each request.
     */
    private function captureResponse()
    {
        $upstreamResponse = $this->client->http->getResponse();
        if ($upstreamResponse !== null) {
            $this->lastResponse = new ResponseWrapper($upstreamResponse);
        }
    }
}
