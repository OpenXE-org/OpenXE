<?php

namespace Xentral\Components\EnvironmentConfig;

final class EnvironmentConfig
{
    /**
     * @var string $databaseHost
     */
    private $databaseHost;

    /**
     * @var string $databaseName
     */
    private $databaseName;

    /**
     * @var string $databaseUser
     */
    private $databaseUser;

    /**
     * @var string $databasePassword
     */
    private $databasePassword;

    /**
     * @var int $databasePort
     */
    private $databasePort;

    /**
     * @var string $userdataDirectoryPath
     */
    private $userdataDirectoryPath;

    /**
     * @param string $databaseHost
     * @param string $databaseName
     * @param string $databaseUser
     * @param string $databasePassword
     * @param int    $databasePort
     * @param string $userdataDirectoryPath
     */
    public function __construct(
        string $databaseHost,
        string $databaseName,
        string $databaseUser,
        string $databasePassword,
        int $databasePort,
        string $userdataDirectoryPath
    ) {
        $this->databaseHost = $databaseHost;
        $this->databaseName = $databaseName;
        $this->databaseUser = $databaseUser;
        $this->databasePassword = $databasePassword;
        $this->databasePort = $databasePort;
        $this->userdataDirectoryPath = $userdataDirectoryPath;
    }

    /**
     * @return string
     */
    public function getDatabaseHost(): string
    {
        return $this->databaseHost;
    }

    /**
     * @return string
     */
    public function getDatabaseName(): string
    {
        return $this->databaseName;
    }

    /**
     * @return string
     */
    public function getDatabaseUser(): string
    {
        return $this->databaseUser;
    }

    /**
     * @return string
     */
    public function getDatabasePassword(): string
    {
        return $this->databasePassword;
    }

    /**
     * @return int
     */
    public function getDatabasePort(): int
    {
        return $this->databasePort;
    }

    /**
     * @return string
     */
    public function getUserdataDirectoryPath(): string
    {
        return $this->userdataDirectoryPath;
    }
}
