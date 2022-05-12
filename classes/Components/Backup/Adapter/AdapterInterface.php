<?php

namespace Xentral\Components\Backup\Adapter;

use Xentral\Components\Database\DatabaseConfig;

/**
 * Interface AdapterInterface
 *
 * @package Xentral\Components\Backup\Adapter
 */
interface AdapterInterface
{
    /** @var string STATUS_WORKING */
    const   STATUS_WORKING = 'working';

    /** @var string STATUS_WAIT */
    const   STATUS_WAITING = 'waiting';

    /**
     * Makes MySQL DUMP
     *
     * @param DatabaseConfig    $config
     *
     * @param string            $file
     *
     * @param null|string|array $sTable
     *
     * @param null|string       $where
     *
     * @param bool              $quickMode Without SET INNODB_STRICT_MODE=0; Advantage quick and space-saving
     *
     * @return int PidFile
     */
    public function createDump(DatabaseConfig $config, $file, $sTable = null, $where = null, $quickMode=true);

    /**
     * Makes Backup or System template recovery
     *
     * @param DatabaseConfig $config
     * @param string         $file
     *
     * @return int pidFile
     */
    public function restoreDump(DatabaseConfig $config, $file);

    /**
     * returns the current status
     *
     * @param string $pidFile
     *
     * @return string|self::STATUS_WORKING|self::STATUS_WAITING
     */
    public function getStatus($pidFile);

}