<?php

declare(strict_types=1);

namespace Xentral\Modules\Log\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\Log\Data\LogData;

final class DatabaseLogGateway
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
     * @param int $logId
     *
     * @return string
     */
    public function tryGetLogDump(int $logId): ?string
    {
        $sql = 'SELECT l.dump FROM `log` AS `l` WHERE l.id = :log_id';
        $value = $this->db->fetchValue($sql, ['log_id' => $logId]);
        if (!is_string($value)) {
            return null;
        }

        return $value;
    }

    /**
     * @param string $cronFileName
     *
     * @return array|LogData[]
     */
    public function findLogsByCronjob(string $cronFileName): array
    {
        $originDetail = 'job=' . $cronFileName;

        $sql =
            'SELECT
            l.id,
            l.log_time,
            l.level,
            l.message,
            l.class,
            l.method,
            l.line,
            l.origin_type,
            l.origin_detail,
            l.dump
            FROM `log` AS `l`
            WHERE l.origin_detail = :origin_detail
            ORDER BY l.id';

        $logs = $this->db->fetchAll($sql, ['origin_detail' => $originDetail]);

        $result = [];
        if (!empty($logs)) {
            foreach ($logs as $log) {
                $result[] = LogData::fromDbState($log);
            }
        }

        return $result;
    }
}
