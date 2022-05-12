<?php

namespace Xentral\Modules\DemoExporter;

use ApplicationCore;
use Xentral\Modules\DemoExporter\Exception\DemoExporterCleanerException;

final class DemoExporterCleanerService
{
    /**
     * @var ApplicationCore
     */
    private $app;

    /**
     *
     * @param ApplicationCore $app
     */
    public function __construct(ApplicationCore $app)
    {
        $this->app = $app;
    }

    /**
     * @param string $data
     *
     * @return string|string[]|null
     */
    public function tryXssClean($data)
    {
        if ($data === null || trim($data) === '') {
            throw new DemoExporterCleanerException('Data is missing! ');
        }

        if ($this->dataNotSQLInjection($data) === false) {
            throw new DemoExporterCleanerException('SQL Injection detected! ');
        }

        return $this->app->stringcleaner->xss_clean($data);
    }

    /**
     * @param string $where
     *
     * @return bool
     */
    private function dataNotSQLInjection($where)
    {

        $disAllow = [
            'UNION',
            'SELECT(.*)INTO(.*)',
            'INSERT',
            'DELETE',
            'UPDATE',
            'LOAD',
            'RENAME',
            'DROP',
            'CREATE',
            'TRUNCATE',
            'ALTER',
            'COMMIT',
            'ROLLBACK',
            'MERGE',
            'CALL',
            'EXPLAIN',
            'LOCK',
            'GRANT',
            'REVOKE',
            'SAVEPOINT',
            'TRANSACTION',
            'SET',
            'USE',
            'SHOW',
        ];

        $disAllowMapped = array_map(static function ($sqlDialect) {
            return '\b' . $sqlDialect . '\b';
        }, $disAllow);
        $disAllowPattern = implode('|', $disAllowMapped);

        return !preg_match("/($disAllowPattern)/i", $where);

    }
}
