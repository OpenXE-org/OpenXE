<?php

namespace Xentral\Components\Filesystem\Adapter;

use Xentral\Components\Filesystem\Exception\InvalidArgumentException;

final class FtpConfig
{
    /** @var string $hostname */
    private $hostname;

    /** @var string $username */
    private $username;

    /** @var string $password */
    private $password;

    /** @var string $rootDir */
    private $rootDir;

    /** @var int $port */
    private $port;

    /** @var int $timeout */
    private $timeout;

    /** @var bool $passive */
    private $passive;

    /** @var bool $ssl */
    private $ssl;

    /**
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $rootDir
     * @param int    $port
     * @param int    $timeout
     * @param bool   $passive
     * @param bool   $ssl
     */
    public function __construct(
        $hostname,
        $username,
        $password,
        $rootDir = '/',
        $port = 21,
        $timeout = 30,
        $passive = true,
        $ssl = false
    ) {
        if (empty($hostname)) {
            throw new InvalidArgumentException('Hostname is empty.');
        }
        if (empty($username)) {
            throw new InvalidArgumentException('Username is empty.');
        }
        if (empty($password)) {
            throw new InvalidArgumentException('Password is empty.');
        }
        if (empty($rootDir)) {
            throw new InvalidArgumentException('Root dir is empty.');
        }

        $this->hostname = (string)$hostname;
        $this->username = (string)$username;
        $this->password = (string)$password;
        $this->rootDir = (string)$rootDir;
        $this->port = (int)$port;
        $this->timeout = (int)$timeout;
        $this->passive = (bool)$passive;
        $this->ssl = (bool)$ssl;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'host'            => $this->hostname,
            'username'        => $this->username,
            'password'        => $this->password,
            'root'            => $this->rootDir,
            'port'            => $this->port,
            'timeout'         => $this->timeout,
            'passive'         => $this->passive,
            'ssl'             => $this->ssl,
            'recurseManually' => true,
        ];
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return null
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @return bool
     */
    public function isPassive()
    {
        return $this->passive;
    }

    /**
     * @return bool
     */
    public function isSsl()
    {
        return $this->ssl;
    }
}
