<?php

namespace Xentral\Modules\Api\Http;

/**
 * @deprecated Use Xentral\Components\Http instead
 */
class ServerParameter extends ParameterCollection
{
    /**
     * @return array
     */
    public function getHeaders()
    {
        $header = [];

        if (isset($this->params['CONTENT_TYPE'])) {
            $header['Content-Type'] = $this->params['CONTENT_TYPE'];
        }

        foreach ($this->params as $name => $value) {
            if (substr($name, 0, 4) === 'HTTP') {
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
     * Header-Bezeichnungen umwandeln
     *
     * @param string $name
     *
     * @return string
     *
     * @example Wandelt "HTTP_USER_AGENT" zu "User-Agent"
     */
    private function transformHeaderName($name)
    {
        $name = substr($name, 5); // HTTP-Prefix entfernen
        $name = str_replace('_', ' ', $name);
        $name = strtolower($name);
        $name = ucwords($name);

        return str_replace(' ', '-', $name);
    }
}
