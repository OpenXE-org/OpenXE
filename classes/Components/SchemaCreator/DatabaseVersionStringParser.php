<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator;

use InvalidArgumentException;

final class DatabaseVersionStringParser
{

    /** @var string */
    private const MYSQL_DB_TYPE = 'mysql';

    /** @var string */
    private const MARIA_DB_TYPE = 'mariadb';

    /** @var string $db */
    private $stringVersion;

    /**
     * @param string $stringVersion
     */
    public function __construct(string $stringVersion)
    {
        if (empty($stringVersion)) {
            throw new InvalidArgumentException('String version cannot be Empty');
        }
        $this->stringVersion = strtolower($stringVersion);
    }

    /**
     * @param string $driver
     *
     * @throw InvalidArgumentException
     *
     * @return bool
     */
    public function isDriver(string $driver): bool
    {
        if ($driver !== self::MARIA_DB_TYPE && strripos($this->stringVersion, 'maria') !== false) {
            return false;
        }

        if ($driver === self::MARIA_DB_TYPE) {
            return strripos($this->stringVersion, 'maria') !== false;
        }

        if ($driver !== self::MYSQL_DB_TYPE) {
            throw new InvalidArgumentException(sprintf('%s is currently not supported', $driver));
        }

        return true;
    }

    /**
     * @return string
     */
    public function getDriverVersion(): string
    {
        $version = $this->isDriver(self::MARIA_DB_TYPE) ? substr($this->stringVersion, 0, 4) : substr(
            $this->stringVersion,
            0,
            3
        );

        if (empty($version) || !is_numeric($version[0])) {
            throw new InvalidArgumentException('Unknown Database Driver Version');
        }

        return $version;
    }

}
