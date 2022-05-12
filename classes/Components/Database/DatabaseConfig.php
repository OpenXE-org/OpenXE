<?php

namespace Xentral\Components\Database;

final class DatabaseConfig
{
    /** @var string $hostname */
    private $hostname;

    /** @var string $username */
    private $username;

    /** @var string $password */
    private $password;

    /** @var string $database */
    private $database;

    /** @var string $charset */
    private $charset;

    /** @var int $port */
    private $port;

    /** @var array $queries */
    private $queries;

    /**
     * @param string      $hostname
     * @param string      $username
     * @param string      $password
     * @param string      $database
     * @param string|null $charset
     * @param int|null    $port
     * @param array       $queries
     */
    public function __construct(
        $hostname,
        $username,
        $password,
        $database,
        $charset = null,
        $port = null,
        array $queries = []
    ) {
        $this->hostname = (string)$hostname;
        $this->username = (string)$username;
        $this->password = (string)$password;
        $this->database = (string)$database;
        $this->charset = $charset !== null ? (string)$charset : 'utf8';
        $this->port = $port !== null ? (int)$port : 3306;
        $this->queries = $queries;
    }

    /**
     * @param array $config
     *
     * @return DatabaseConfig
     */
    public static function fromArray(array $config)
    {
        return new DatabaseConfig(
            $config['hostname'],
            $config['username'],
            $config['password'],
            $config['database'],
            isset($config['charset']) ? $config['charset'] : null,
            isset($config['port']) ? $config['port'] : null,
            isset($config['queries']) ? $config['queries'] : []
        );
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
     * @return string
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return array
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'args' => [
                'hostname' => $this->hostname,
                'username' => '****',
                'password' => '****',
                'database' => $this->database,
                'charset'  => $this->charset,
                'port'     => $this->port,
                'queries'  => $this->queries,
            ],
        ];
    }
}
