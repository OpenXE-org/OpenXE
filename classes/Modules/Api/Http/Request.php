<?php

namespace Xentral\Modules\Api\Http;

use Xentral\Modules\Api\Http\Exception\MethodNotAllowedException;

/**
 * @deprecated Use Xentral\Components\Http instead
 */
class Request
{
    /** @var array $supportedMethods */
    protected static $supportedMethods = [
        'GET', 'POST', 'PUT', 'DELETE',
    ];

    /** @var array $attributes */
    public $attributes;

    /** @var array $query $_GET-Parameter */
    public $query;

    /** @var array $request $_POST-Parameter */
    public $request;

    /** @var array $server $_SERVER-Parameter */
    public $server;

    /** @var array $headers */
    public $headers;

    /** @var string $method */
    protected $method;

    /** @var string $pathInfo */
    protected $pathInfo;

    /** @var string $requestUri */
    protected $requestUri;

    /** @var string $content */
    protected $content;

    /** @var array $acceptableContentTypes */
    protected $acceptableContentTypes;

    /**
     * @param array  $query
     * @param array  $request
     * @param array  $server
     * @param array  $files
     * @param array  $cookies
     * @param string $content
     */
    public function __construct(
        array $query = [],
        array $request = [],
        array $server = [],
        array $files = [],
        array $cookies = [],
        $content = null
    ) {
        $this->query = new ParameterCollection(!empty($query) ? $query : $_GET);
        $this->request = new ParameterCollection(!empty($request) ? $request : $_POST);
        $this->server = new ServerParameter(!empty($server) ? $server : $_SERVER);
        // $this->files = $_FILES; // @todo
        // $this->cookies = $_COOKIE; // @todo
        $this->attributes = new ParameterCollection([]);
        $this->headers = new ParameterCollection($this->server->getHeaders());

        $this->method = $this->getMethod();
        $this->requestUri = $this->getRequestUri();
        $this->pathInfo = $this->getPathInfo();
        $this->content = $content;
    }

    /**
     * @return Request
     */
    public static function createFromGlobals()
    {
        return new static($_GET, $_POST, $_SERVER, [], []);
    }

    /**
     * @deprecated Use Xentral\Tests\Http\RequestFactory instead
     *
     * @param string $uri
     * @param string $method
     * @param array  $params $_GET oder $_POST-Parameter
     * @param array  $server
     * @param string $content
     *
     * @return Request
     */
    public static function create($uri, $method = 'GET', $params = [], $server = [], $content = null)
    {
        // Default-Settings
        $serverDefault = [
            'HTTP_HOST' => 'localhost',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'PATH_INFO' => '',
            'REMOTE_ADDRESS' => '127.0.0.1',
            'REQUEST_METHOD' => 'GET',
            'REQUEST_SCHEME' => 'http',
            'REQUEST_TIME' => time(),
            'SCRIPT_NAME' => '',
            'SCRIPT_FILENAME' => '',
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => '80',
            'SERVER_PROTOCOL' => 'HTTP/1.1'
        ];

        $server = array_merge($serverDefault, $server);

        if ($method !== 'GET' && in_array($method, self::$supportedMethods, true)) {
            $server['REQUEST_METHOD'] = strtoupper($method);
        }

        $queryParams = [];
        $requestParams = [];
        if ($method === 'GET') {
            $queryParams = $params;
        } elseif (in_array($method, ['POST', 'PUT'])) {
            $requestParams = $params;
        }

        $uriParts = parse_url($uri);

        if (!empty($uriParts['scheme'])) {
            $server['REQUEST_SCHEME'] = $uriParts['scheme'];
        }

        if (!empty($uriParts['host'])) {
            $server['HTTP_HOST'] = $uriParts['host'];
            $server['SERVER_NAME'] = $uriParts['host'];
        }

        if (!empty($uriParts['port'])) {
            $server['SERVER_PORT'] = (string)$uriParts['port'];
            $server['HTTP_HOST'] .= ':' . $uriParts['port'];
        }

        if (!isset($uriParts['path'])) {
            $uriParts['path'] = '/';
        }

        $server['REQUEST_URI'] = $uriParts['path'];

        $queryString = '';
        if (!empty($uriParts['query'])) {
            $queryString = $uriParts['query'];

            // @todo URL-Parameter und $queryParams zusammenführen

        } else {
            if (!empty($queryParams)) {
                $queryString = http_build_query($queryParams, '', '&');
            }
        }

        $server['QUERY_STRING'] = $queryString;
        if (!empty($queryString)) {
            $server['REQUEST_URI'] .= '?' . $queryString;
        }

        return new static($queryParams, $requestParams, $server, [], [], $content);
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

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        if (null === $this->method) {
            $method = strtoupper($this->server->get('REQUEST_METHOD') ?: 'GET');
            if (!in_array($method, self::$supportedMethods, true)) {
                throw new MethodNotAllowedException(self::$supportedMethods);
            }
            $this->method = $method;
        }

        return $this->method;
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        if (null === $this->requestUri) {
            $this->requestUri = $this->server->get('REQUEST_URI');
        }

        return $this->requestUri;
    }

    /**
     * @return string
     */
    public function getPathInfo()
    {
        if (null === $this->pathInfo) {
            $this->pathInfo = !empty($this->server->get('PATH_INFO')) ? $this->server->get('PATH_INFO') : '/';
        }

        return $this->pathInfo;
    }

    /**
     * @deprecated Use PathInfoDetector instead
     *
     * Gibt den berechneten PathInfo-Teil der URL zurück; ohne $_SERVER['PATH_INFO'] zu verwenden
     *
     * Wird benötigt um Fehler in der Server-Konfiguration zu erkennen
     *
     * @return string|false false wenn PathInfo nicht rekonstruiert werden kann
     */
    public function getDetectedPathInfo()
    {
        $scriptName = $this->getSafeScriptName();
        if (empty($scriptName)) {
            return false; // Fehlerhafte Webserver-Konfiguration
        }

        // PathInfo aus $_SERVER['DOCUMENT_URI'] ermitteln
        // Bei Apache nicht gesetzt! Nur bei Nginx und PHP-FPM gesetzt; abhängig von Konfiguration!
        $docUri = $this->server->get('DOCUMENT_URI');
        if (!empty($docUri) && strpos($docUri, $scriptName) === 0) {
            return substr($docUri, strlen($scriptName));
        }

        // PathInfo aus $_SERVER['PHP_SELF'] ermitteln
        $phpSelf = $this->server->get('PHP_SELF');
        if (strpos($phpSelf, $scriptName) === 0) {
            return substr($phpSelf, strlen($scriptName));
        }

        // PathInfo aus $_SERVER['REQUEST_URI'] ermitteln; ohne URL-Rewriting
        // Request-URI kann Query-Parameter enthalten!
        $reqUri = $this->server->get('REQUEST_URI');
        if (!empty($reqUri) && strpos($reqUri, $scriptName) === 0) {
            $pathInfoWithQueryParams = substr($reqUri, strlen($scriptName));

            return $this->trimQueryParams($pathInfoWithQueryParams);
        }

        // Komplexeres URL-Rewriting, oder fehlerhafte Webserver-Konfiguration
        // => PathInfo kann nicht rekonstruiert werden
        return false;
    }

    /**
     * Ermittelt $_SERVER['SCRIPT_NAME'] ohne PathInfo
     *
     * Unter Nginx + PHP-FPM kann(!) der $_SERVER['SCRIPT_NAME'] auch den PathInfo enthalten.
     *
     * @return string
     */
    private function getSafeScriptName()
    {
        $scriptFilename = $this->server->get('SCRIPT_FILENAME');
        $documentRoot = $this->server->get('DOCUMENT_ROOT');

        if (strpos($scriptFilename, $documentRoot) === 0) {
            return substr($scriptFilename, strlen($documentRoot));
        }

        return $this->server->get('SCRIPT_NAME');
    }
    
    /**
     * @param bool $withQueryParams GET-Parameter mitliefern?
     *
     * @return string
     */
    public function getFullUri($withQueryParams = true)
    {
        $scheme = $this->server->get('REQUEST_SCHEME');
        $hostAndPort = $this->server->get('HTTP_HOST');
        $requestUri = $this->server->get('REQUEST_URI');

        $fullUriWithQueryParams = sprintf('%s://%s%s', $scheme, $hostAndPort, $requestUri);
        if ($withQueryParams === true) {
            return $fullUriWithQueryParams;
        }

        /*
         * Nachfolgend werden die GET-Parameter aus der Uri entfernt
         */

        $offset = strpos($fullUriWithQueryParams, '?');
        $fullUriWithoutQueryParams = $offset !== false
            ? substr_replace($fullUriWithQueryParams, '', $offset)
            : $fullUriWithQueryParams;

        // Query-String zerlegen
        $queryString = $this->server->get('QUERY_STRING');
        parse_str($queryString, $queryParts);

        /** @see /www/api/docs.html#failsafe */
        if (isset($queryParts['path'])) {
            return $fullUriWithoutQueryParams . '?path=' . $queryParts['path'];
        }

        return $fullUriWithoutQueryParams;
    }

    /**
     * Beispiel-Failsafe-Uri: /api/index.php?path=/v1/adressen
     *
     * @see /www/api/docs.html#failsafe
     *
     * @return bool
     */
    public function isFailsafeUri()
    {
        $queryString = $this->server->get('QUERY_STRING');
        parse_str($queryString, $queryParts);

        return isset($queryParts['path']);
    }

    /**
     * @return string|null [json|xml|html|...] oder null wenn nicht gesetzt
     */
    public function getContentType()
    {
        $contentTypeRaw = $this->headers->get('Content-Type');
        if (null === $contentTypeRaw) {
            return null;
        }

        $typeParts = explode('/', strtolower($contentTypeRaw));

        return $typeParts[1];
    }

    /**
     * @return string
     */
    public function getContent()
    {
        if (null === $this->content) {
            $this->content = file_get_contents('php://input');
        }

        return !empty($this->content) ? $this->content : '';
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = (string)$content;
    }

    /**
     * @return array
     */
    public function getAcceptableContentTypes()
    {
        if (null === $this->acceptableContentTypes) {
            $acceptHeaderRaw = $this->headers->get('Accept');
            $acceptParts = explode(',', $acceptHeaderRaw);

            $acceptable = [];
            foreach ($acceptParts as $acceptPart) {
                if ($pos = strpos($acceptPart, ';')) {
                    // Priorität abschneiden
                    $acceptPart = substr($acceptPart, 0, $pos);
                }
                $acceptable[] = $acceptPart;
            }

            $this->acceptableContentTypes = $acceptable;
        }

        return $this->acceptableContentTypes;
    }

    /**
     * @param string $url
     *
     * @return string URL ohne Query-Parameter
     */
    protected function trimQueryParams($url)
    {
        $queryParamsOffset = strpos($url, '?');
        if ($queryParamsOffset === false) {
            return $url; // Keine Query-Parameter vorhanden
        }

        return substr($url, 0, $queryParamsOffset);
    }
}
