<?php

declare(strict_types=1);

namespace Xentral\Modules\Log\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\Log\Exception\InvalidArgumentException;

final class DatabaseLogService
{
    /** @var Database $db */
    private $db;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * Deletes all log entries.
     *
     * @return int Amount of deleted log entries
     */
    public function removeAllLogs(): int
    {
        $sql = 'DELETE FROM `log` WHERE 1';

        return $this->db->fetchAffected($sql);
    }

    /**
     * Removes log entries that are older than specified amount of days.
     *
     * @param int $days
     *
     * @throws InvalidArgumentException
     *
     * @return int Amount of deleted log entries
     */
    public function removeLogsOlderThan(int $days): int
    {
        if ($days < 1) {
            throw new InvalidArgumentException('Negative days not acceptable.');
        }
        $sql = 'DELETE FROM `log` WHERE DATE_SUB(CURDATE(), INTERVAL :days DAY) >= log_time';

        return $this->db->fetchAffected($sql, ['days' => $days]);
    }
}
