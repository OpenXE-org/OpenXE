<?php

namespace Xentral\Components\Http;

use Xentral\Components\Http\Collection\FilesCollection;
use Xentral\Components\Http\Collection\ParameterCollection;
use Xentral\Components\Http\Collection\ReadonlyParameterCollection;
use Xentral\Components\Http\Collection\ServerParameter;
use Xentral\Components\Http\Exception\HttpException;
use Xentral\Components\Http\Exception\InvalidArgumentException;
use Xentral\Components\Http\Exception\MethodNotAllowedException;
use Xentral\Components\Http\File\FileUpload;

class Request
{
    /** @var array $supportedMethods */
    protected static $supportedMethods = [
        'GET',
        'POST',
        'PUT',
        'DELETE',
    ];

    /** @var ReadonlyParameterCollection $get $_GET-Parameter */
    public $get;

    /** @var ReadonlyParameterCollection $post $_POST-Parameter */
    public $post;

    /** @var ReadonlyParameterCollection $cookie $_COOKIE-Parameter */
    public $cookie;

    /** @var FilesCollection $files $_FILES-Parameter */
    public $files;

    /** @var ServerParameter $server $_SERVER-Parameter */
    public $server;

    /** @var ReadonlyParameterCollection $header */
    public $header;

    /** @var ParameterCollection $attributes Custom request attributes (e.g. Router arguments) */
    public $attributes;

    /** @var string $method */
    protected $method;

    /** @var string $pathInfo */
    protected $pathInfo;

    /** @var string $requestUri */
    protected $requestUri;

    /** @var string|resource|null $content */
    protected $content;

    /** @var array $acceptableContentTypes */
    protected $acceptableContentTypes;

    /**
     * @param array                $get
     * @param array                $post
     * @param array                $files
     * @param array                $server
     * @param array                $cookie
     * @param string|resource|null $content
     */
    public function __construct(
        array $get = [],
        array $post = [],
        array $files = [],
        array $server = [],
        array $cookie = [],
        $content = null
    ) {
        $this->get = new ReadonlyParameterCollection(!empty($get) ? $get : (array)$_GET);
        $this->post = new ReadonlyParameterCollection(!empty($post) ? $post : (array)$_POST);
        $this->files = new FilesCollection(!empty($files) ? $files : (array)$_FILES);
        $this->server = new ServerParameter(!empty($server) ? $server : (array)$_SERVER);
        $this->cookie = new ReadonlyParameterCollection(!empty($cookie) ? $cookie : (array)$_COOKIE);
        $this->header = new ReadonlyParameterCollection($this->server->getHeaders());
        $this->attributes = new ParameterCollection([]);
        $this->method = $this->getMethod();
        $this->requestUri = $this->getRequestUri();
        $this->pathInfo = $this->getPathInfo();
        $this->content = $content;
    }

    /**
     * Returns an instance of Request created with php's superglobals.
     *
     * @param string|null $content
     *
     * @return Request
     */
    public static function createFromGlobals($content = null)
    {
        $request = new static((array)$_GET, (array)$_POST, (array)$_FILES, (array)$_SERVER, (array)$_COOKIE, $content);

        if (
            $request->server->get('CONTENT_TYPE') === 'application/x-www-form-urlencoded' &&
            strtoupper($request->server->get('REQUEST_METHOD')) === 'PUT'
        ) {
            parse_str($request->getContent(), $postParams);
            $request->post = new ReadonlyParameterCollection($postParams);
        }

        return $request;
    }

    /**
     * Gets a $_GET parameter value
     *
     * @param string     $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getGet($name, $default = null)
    {
        return $this->get->get($name, $default);
    }

    /**
     * Gets a $_POST parameter value
     *
     * @param string     $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getPost($name, $default = null)
    {
        return $this->post->get($name, $default);
    }

    /**
     * Gets a $_GET or $_POST parameter value
     *
     * Looks at $_GET parameters first.
     *
     * @param string     $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getParam($name, $default = null)
    {
        if ($this->get->has($name)) {
            return $this->get->get($name);
        }

        if ($this->post->has($name)) {
            return $this->post->get($name);
        }

        return $default;
    }

    /**
     * Gets a $_FILES parameter value
     *
     * @param string     $name
     * @param mixed|null $default
     *
     * @return FileUpload|mixed
     */
    public function getFile($name, $default = null)
    {
        return $this->files->get($name, $default);
    }

    /**
     * Gets a $_SERVER parameter value
     *
     * @param string     $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getServer($name, $default = null)
    {
        return $this->server->get($name, $default);
    }

    /**
     * Gets a HTTP header value
     *
     * Use the HTTP header name as used in the Browser.
     *
     * @example getHeader('Content-Type') returns 'text'
     * @example getHeader('CONTENT_TYPE') returns null
     *
     * @param string     $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getHeader($name, $default = null)
    {
        return $this->header->get($name, $default);
    }

    /**
     * Gets a $_COOKIE parameter value
     *
     * @param string     $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getCookie($name, $default = null)
    {
        return $this->cookie->get($name, $default);
    }

    /**
     * Returns true if a secure protocol was used.
     *
     * @return bool
     */
    public function isSecure()
    {
        if ($this->server->get('HTTPS') === 'on') {
            return true;
        }
        if ($this->server->get('HTTP_X_FORWARDED_SSL') === 'on') {
            return true;
        }
        if ($this->server->get('HTTP_X_FORWARDED_PROTO') === 'https') {
            return true;
        }

        return false;
    }

    /**
     * Returns true if the request is an ajax request.
     *
     * @return bool
     */
    public function isAjax()
    {
        return strtolower($this->server->get('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest';
    }

    /**
     * Returns true if the request was issued via command line.
     *
     * @return bool
     */
    public function isCli()
    {
        return php_sapi_name() === 'cli';
    }

    /**
     * Gets the HTTP method.
     *
     * @return string
     */
    public function getMethod()
    {
        if ($this->method === null) {
            $method = strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
            if (!in_array($method, self::$supportedMethods, true)) {
                throw new MethodNotAllowedException(self::$supportedMethods);
            }
            $this->method = $method;
        }

        return $this->method;
    }

    /**
     * Gets the request URI.
     *
     * Same as $_SERVER['REQUEST_URI']
     *
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
     * Get path info from the request.
     *
     * @return string
     */
    public function getPathInfo()
    {
        if (null === $this->pathInfo) {
            $this->pathInfo = !empty($this->server->get('PATH_INFO')) ? $this->server->get('PATH_INFO') : '';
        }

        return $this->pathInfo;
    }

    /**
     * Returns protocol and hostname.
     *
     * @example 'http://www.xentral.com'
     *
     * @return string
     */
    public function getSchemeAndHttpHost()
    {
        $protocol = 'http';
        if ($this->isSecure() || strtolower($this->server->get('REQUEST_SCHEME')) === 'https') {
            $protocol = 'https';
        }
        return sprintf(
            '%s://%s',
            $protocol,
            $this->server->get('HTTP_HOST')
        );
    }

    /**
     * Returns the full URL with GET parameters.
     *
     * @example ->'http://www.xentral.com/path/index.php?param=1&param2=2'
     *
     * @return string
     */
    public function getFullUrl()
    {
        return $this->getSchemeAndHttpHost() . $this->getRequestUri();
    }

    /**
     * Returns the URL without GET parameters.
     *
     * @example 'http://www.xentral.com/path/index.php?var=1' -> 'http://www.xentral.com/path'
     * @example 'http://www.xentral.com/path/?var=1' -> 'http://www.xentral.com/path'
     * @example 'http://www.xentral.com/path?var=1' -> 'http://www.xentral.com'
     *
     * @return string
     */
    public function getBaseUrl()
    {
        $baseUrl = '';
        $uri = $this->getRequestUri();
        $uri = preg_replace('/([^?]+)[?]?.*/', '\1', $uri);

        $uriParts = explode('/', $uri);
        for ($i=0; $i < count($uriParts)-1; $i++) {
            if( $uriParts[$i] !== '') {
               $baseUrl .= '/' . $uriParts[$i];
            }
        }

        $baseUrl = $this->getSchemeAndHttpHost() . $baseUrl;

        //Failsafe?
//        $queryString = $this->server->get('QUERY_STRING');
//        parse_str($queryString, $queryParts);
//
//        /** @see /www/api/docs.html#failsafe */
//        if (isset($queryParts['path'])) {
//            return sprintf('%s?path=%s', $baseUrl, $queryParts['path']);
//        }

        return $baseUrl;
    }

    /**
     * Appends specified path to the URL.
     *
     * @example getUrlForPath('/path') -> 'http://www.xentral.com/path'
     *
     * @param string $path must starts with a slash '/'
     *
     * @return string
     */
    public function getUrlForPath($path)
    {
        if (!preg_match('/^\/.*$/', $path)) {
            throw  new InvalidArgumentException('The first argument must start with a slash "/"');
        }

        $schemeAndHost = $this->getSchemeAndHttpHost();
        $urlPath = preg_replace('/^(.*)\/[^\/]*$/', '\1', $this->server->get('SCRIPT_NAME'));

        return sprintf('%s%s%s', $schemeAndHost, $urlPath, $path);
    }

    /**
     *Returns the path between the URI and the current SCRIPT_NAME
     *
     * @example 'http://www.xentral.com/www/api/v1/dateien/50' -> '/v1/dateien/50'
     *
     * @return string
     */
    public function getBasePath()
    {
        $basePath = '';
        $scriptNameParts = explode('/', $this->server->get('SCRIPT_NAME'));
        $uri =  preg_replace('/([^?]+)[?]?.*/', '\1',  $this->server->get('REQUEST_URI'));
        $uriParts = explode('/', $uri);

        for($i=0; $i < count($uriParts)-0; $i++) {
            if (!isset($scriptNameParts[$i]) || $scriptNameParts[$i] !== $uriParts[$i]) {
                $basePath .= '/'. $uriParts[$i];
            }
        }

        if($basePath === '') {
            $basePath = '/';
        }

        return $basePath;
    }

    /**
     * @deprecated Use getFullUrl or getBaseUrl instead!
     *
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
     * Returns true if the failsafe URL was used for the request
     *
     * @example Failsafe-Uri: /api/index.php?path=/v1/adressen
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
     * Returns the request body.
     *
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
     * Returns the content type of the request.
     *
     * @return string|null [json|xml|html|...] null if not set
     */
    public function getContentType()
    {
        $contentTypeRaw = $this->header->get('Content-Type');
        if ($contentTypeRaw === null || $contentTypeRaw === '') {
            return null;
        }

        // Boundary bei Multipart Content-Type entfernen
        // @example "Content-Type: multipart/form-data; boundary=gc0p4Jq0M2Yt08jU534c0p"
        $posSemicolon = strpos($contentTypeRaw, ';');
        if ($posSemicolon !== false) {
            $contentTypeRaw = trim(substr($contentTypeRaw, 0, $posSemicolon));
        }

        $typeParts = explode('/', strtolower($contentTypeRaw));
        if (count($typeParts) < 2) {
            throw new HttpException(400, sprintf('Invalid content type "%s".', $contentTypeRaw));
        }

        return $typeParts[1];
    }

    /**
     * Gets the data types from the HTTP Accept header.
     *
     * @return array [] if not set
     */
    public function getAcceptableContentTypes()
    {
        if (null === $this->acceptableContentTypes) {
            $acceptHeaderRaw = $this->header->get('Accept');
            $acceptParts = explode(',', $acceptHeaderRaw);

            $acceptable = [];
            foreach ($acceptParts as $acceptPart) {
                if ($pos = strpos($acceptPart, ';')) {
                    // Priorität abschneiden
                    $acceptPart = substr($acceptPart, 0, $pos);
                }
                $acceptable[] = trim($acceptPart);
            }

            $this->acceptableContentTypes = $acceptable;
        }

        return $this->acceptableContentTypes;
    }
}
