<?php


namespace Xentral\Components\Backup;


use Xentral\Components\Backup\Exception\BackupException;

interface FileBackupInterface
{
    /** @var string STATUS_WAIT */
    const STATUS_WAITING = 'waiting';
    /** @var string STATUS_WORKING */
    const STATUS_WORKING = 'working';
    /** @var string Extension for the whole backup */
    const COMPRESS_EXTENSION = 'zip';
    /** @var string pid file */
    const PID_FILE = 'backup.lock';
    /** @var int Timeout */
    const TIME_OUT = 3600;
    /** @var string snapshots folder */
    const SNAPSHOTS_FOLDER = 'backup/snapshots';
    /** @var string user data directory */
    const LOCAL_FILES_DIR_NAME = 'userdata';

    /**
     * @param string|null $userPath
     *
     * @throws BackupException
     * @return string|null
     */
    public function begin($userPath = null);

    /**
     * @param string      $filename
     * @param string      $userPath
     * @param string|null $sMySQLFile
     *
     * @return bool
     */
    public function createBackup($filename, $userPath, $sMySQLFile = null);

    /**
     * @param string $backupFile
     * @param string $userPath
     * @param array  $options
     *
     * @return bool
     */
    public function restoreFileSystem($backupFile, $userPath, $options = []);

    /**
     * @return string
     */
    public function getLockStatus();
}