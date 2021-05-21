<?php

namespace Xentral\Components\Backup;

use Xentral\Components\Backup\Adapter\AdapterInterface;
use Xentral\Components\Database\DatabaseConfig;
use Xentral\Components\Backup\Exception\BackupException;

final class DatabaseBackup
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    /** @var string $tmpPath */
    private $tmpPath;

    /** @var string lock */
    const PID_NAME = 'backup.lock';

    /**
     * DatabaseBackup constructor.
     *
     * @param AdapterInterface $adapter
     *
     * @param string           $tmpPath
     */
    public function __construct(AdapterInterface $adapter, $tmpPath)
    {
        $this->adapter = $adapter;
        $this->tmpPath = $tmpPath;
    }

    /**
     * Creates MySQL Dump
     *
     * @param DatabaseConfig    $config
     * @param string            $file
     * @param null|string|array $sTable
     *
     * @param null|string       $where
     *
     * @return void
     */
    public function createDump(DatabaseConfig $config, $file, $sTable = null, $where = null)
    {
        $sPidFile = $this->getLockFile();
        file_put_contents($sPidFile, time());
        $this->adapter->createDump($config, $file, $sTable, $where);
        @unlink($sPidFile);
    }

    /**
     * Restores Database DUMP
     *
     * @param DatabaseConfig $config
     * @param string         $file
     *
     * @return void
     */
    public function restoreDump(DatabaseConfig $config, $file)
    {
        if (!file_exists($file)) {
            throw new BackupException(sprintf('Database Dump %s not found!', $file));
        }
        $sPidFile = $this->getLockFile();
        file_put_contents($sPidFile, time());
        $this->adapter->restoreDump($config, $file);
        @unlink($sPidFile);
    }

    /**
     * @param string $metaFile
     *
     * @return string|null
     */
    public function getMetaInfo($metaFile)
    {
        if (!empty($metaFile) && file_exists($metaFile) && ($sMetaEnc = file_get_contents($metaFile))) {
            return $this->decodeJson(base64_decode($sMetaEnc), true);
        }

        return null;
    }

    /**
     * @param string $sJSON
     * @param bool   $bAsHash
     *
     * @return mixed|null
     */
    protected function decodeJson($sJSON, $bAsHash = false)
    {
        if (($xData = json_decode($sJSON, $bAsHash)) !== null
            && (json_last_error() === JSON_ERROR_NONE)) {
            return $xData;
        }

        return null;
    }

    /**
     *
     * @return string|AdapterInterface
     */
    public function getLockStatus()
    {
        return $this->adapter->getStatus($this->getLockFile());
    }

    /**
     * @return string
     */
    protected function getLockFile()
    {
        return rtrim($this->tmpPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . static::PID_NAME;
    }

    /**
     * @param array $tables
     * @param array $excludeKeys
     *
     * @return mixed
     */
    public function excludeCheckSumTables($tables, $excludeKeys = [])
    {
        $default = [
            'backup',
            'useronline',
            'logfile',
            'cronjob_starter_running',
            'wiki',
            'protokoll',
            'cronjob_log',
            'module_stat',
            'checkaltertable',
            'konfiguration',
            'permissionhistory',
            'adapterbox_request_log',
            'hook',
            'module_action',
            'prozessstarter',
            'sqlcache',
            'systemhealth',
            'userkonfiguration',
            'artikel',
            'shopimport_amazon_throttling',
            'lieferschein',
            'report_column',
            'report_parameter',
            'notification_message',
            'module_stat_detail',
        ];
        $excludeKeys = array_merge($default, $excludeKeys);
        foreach ($excludeKeys as $key) {
            if (!array_key_exists($key, $tables)) {
                continue;
            }
            unset($tables[$key]);
        }

        return $tables;
    }

    /**
     * @param string $backupFile
     *
     * @return string
     */
    public function getMetaFileName($backupFile)
    {
        $asFile = explode('.', $backupFile);
        array_pop($asFile);
        $filename = implode('.', $asFile) . '.meta';

        return str_replace('.backup', 'backup/snapshots', $filename);
    }

    /**
     * @param string $filePath
     *
     * @return string|null
     */
    public function getDumpMetaData($filePath = null)
    {
        return $this->getMetaInfo($this->getMetaFileName($filePath));
    }

}