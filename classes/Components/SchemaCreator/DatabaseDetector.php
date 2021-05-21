<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator;

final class DatabaseDetector
{
    /** @var DatabaseVersionStringParser $dbVersion */
    private $dbVersion;

    /**
     * @param DatabaseVersionStringParser $dbVersion
     */
    public function __construct(DatabaseVersionStringParser $dbVersion)
    {
        $this->dbVersion = $dbVersion;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->dbVersion->getDriverVersion();
    }

    /**
     * @return bool
     */
    public function isMariaDb(): bool
    {
        return $this->dbVersion->isDriver('mariadb');
    }

    /**
     * @return bool
     */
    public function isMySQL(): bool
    {
        return $this->dbVersion->isDriver('mysql');
    }
}
