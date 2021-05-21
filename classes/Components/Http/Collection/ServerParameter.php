<?php

namespace Xentral\Components\Http\Collection;

class ServerParameter extends ReadonlyParameterCollection
{
    /**
     * Returns all http request headers
     *
     * Emulates php's getallheaders() function
     * getallheaders is not available in all environments
     *
     * @return array
     */
    public function getHeaders()
    {
        $header = [];

        if (isset($this->params['CONTENT_TYPE'])) {
            $header['Content-Type'] = $this->params['CONTENT_TYPE'];
        }

        foreach ($this->params as $name => $value) {
            if (strpos($name, 'HTTP_') === 0) {
                $header[$this->transformHeaderName($name)] = $value;
            }
        }

        // Auth-Header ist bereits gesetzt durch $_SERVER[HTTP_AUTHORIZATION]
        if (!empty($header['Authorization'])) {
            return $header;
        }

        // Basic-Auth
        if (isset($this->params['PHP_AUTH_USER'])) {
            $authString = base64_encode($this->params['PHP_AUTH_USER'] . ':' . $this->params['PHP_AUTH_PW']);
            $header['Authorization'] = sprintf('Basic %s', $authString);
        }

        // Digest-Auth
        if (isset($this->params['PHP_AUTH_DIGEST'])) {
            $header['Authorization'] = sprintf('Digest %s', $this->params['PHP_AUTH_DIGEST']);
        }

        return $header;
    }

    /**
     * Transform header names
     *
     * Transforms php $_SERVER formattet header names to
     * Browser style formatted header names.
     *
     * @example Transforms "HTTP_USER_AGENT" to "User-Agent"
     *
     * @param string $name
     *
     * @return string
     */
    private function transformHeaderName($name)
    {
        $name = substr($name, 5); // HTTP-Prefix entfernen
        $name = (string)str_replace('_', ' ', $name);
        $name = strtolower($name);
        $name = ucwords($name);

        return str_replace(' ', '-', $name);
    }
}
