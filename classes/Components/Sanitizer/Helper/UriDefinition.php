<?php

namespace Xentral\Components\Sanitizer\Helper;

final class UriDefinition
{
    /** @var string|null $scheme */
    private $scheme;

    /** @var string|null $username */
    private $username;

    /** @var string|null $password */
    private $password;

    /** @var string|null $host */
    private $host;

    /** @var int|null $port */
    private $port;

    /** @var string|null $path */
    private $path;

    /** @var array $queryParams */
    private $queryParams = [];

    /** @var string|null $fragment */
    private $fragment;

    /**
     * @param string|null $scheme
     * @param string|null $username
     * @param string|null $password
     * @param string|null $host
     * @param int|null    $port
     * @param string|null $path
     * @param array|null  $queryParams
     * @param string|null $fragment
     */
    public function __construct(
        $scheme = null,
        $username = null,
        $password = null,
        $host = null,
        $port = null,
        $path = null,
        $queryParams = null,
        $fragment = null
    ) {
        if (!empty($scheme)) {
            $this->scheme = strtolower($scheme);
        }
        if (!empty($username)) {
            $this->username = (string)$username;
        }
        if (!empty($password)) {
            $this->password = (string)$password;
        }
        if (!empty($host)) {
            $this->host = (string)$host;
        }
        if (!empty($port)) {
            $this->port = (int)$port;
        }
        if (!empty($path)) {
            $this->path = (string)$path;
        }
        if (is_array($queryParams)) {
            $this->queryParams = $queryParams;
        }
        if (!empty($fragment)) {
            $this->fragment = (string)$fragment;
        }
    }

    /**
     * @return string|null
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @return string|null
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int|null
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string|null
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string|null
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $name
     *
     * @return string|null
     */
    public function getQueryParam($name)
    {
        return isset($this->queryParams[$name]) ? $this->queryParams[$name] : null;
    }

    /**
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * @return string|null
     */
    public function getFragment()
    {
        return $this->fragment;
    }
}
