<?php

namespace Xentral\Modules\Backup;

use Config;
use DateInterval;
use DateTime;
use Exception;
use Xentral\Components\Backup\Adapter\AdapterInterface;
use Xentral\Components\Backup\FileBackup;
use Xentral\Components\Backup\DatabaseBackup;
use Xentral\Components\Backup\Logger\BackupLog;
use Xentral\Components\Database\Database;
use Xentral\Components\Database\DatabaseConfig;
use Xentral\Components\Database\Exception\DatabaseExceptionInterface;
use Xentral\Modules\Backup\Exception\BackupProcessStarterException;
use ZipArchive;
use Xentral\Modules\Backup\Exception\RuntimeException;

/**
 * Class BackupService
 *
 * @package Xentral\Modules\Backup
 */
final class BackupService
{
    /**
     * @var BackupGateway
     */
    private $gateway;
    /**
     * @var DatabaseBackup
     */
    private $oDbBackup;
    /**
     * @var FileBackup
     */
    private $oFileBackup;

    /** @var string */
    const STATUS_FILE = 'status.txt';

    /** @var string */
    const SESSION_FILE = 'session.txt';

    /** @var Database $db */
    private $db;

    /** @var  BackupProcessStarterService $processStarter */
    private $processStarter;

    /** @var  BackupSystemConfigurationService $backupSystemConfiguration */
    private $backupSystemConfiguration;

    /** @var BackupLog $logger */
    private $logger;

    /** @var int min free disk in Bytes */
    const MIN_FREE_DISK = 1073741824;

    private static $publicSubdirectories = ['dms', 'pdfarchiv', 'pdfmirror', 'emailbackup', 'tmp', 'uebertragung'];

    /**
     * BackupService constructor.
     *
     * @param BackupGateway                    $gateway
     * @param DatabaseBackup                   $oDbBackup
     * @param FileBackup                       $oFileBackup
     * @param BackupProcessStarterService      $processStarter
     * @param BackupSystemConfigurationService $backupSystemConfiguration
     * @param Database                         $database
     * @param BackupLog                        $logger
     */
    public function __construct(
        BackupGateway $gateway,
        DatabaseBackup $oDbBackup,
        FileBackup $oFileBackup,
        BackupProcessStarterService $processStarter,
        BackupSystemConfigurationService $backupSystemConfiguration,
        Database $database,
        BackupLog $logger
    ) {
        $this->gateway = $gateway;
        $this->oDbBackup = $oDbBackup;
        $this->oFileBackup = $oFileBackup;
        $this->processStarter = $processStarter;
        $this->backupSystemConfiguration = $backupSystemConfiguration;
        $this->db = $database;
        $this->logger = $logger;
    }

    /**
     * @param string $file
     *
     * @return false|string
     */
    protected function generateMetaData($file)
    {
        /** @var  $adminIds */
        $adminIds = $this->gateway->getAdminUserIds();
        $hMeta = [
            'tables'  => $this->gateway->getTablesChecksum(),
            'users'   => !empty($adminIds) ? array_column($adminIds, 'id') : [],
            'created' => time(),
            'name'    => $file,
        ];

        return json_encode($hMeta);
    }

    /**
     * @param string $file
     *
     * @return false|int
     */
    protected function addMetadata($file)
    {
        $sMetaFile = $this->oDbBackup->getMetaFileName($file);
        $sMeta = base64_encode($this->generateMetaData($sMetaFile));

        return file_put_contents($sMetaFile, $sMeta);
    }

    /**
     * @param Config $config
     *
     * @return DatabaseConfig
     */
    public function convertLegacyDbConf(Config $config)
    {
        return new DatabaseConfig(
            $config->WFdbhost,
            $config->WFdbuser,
            $config->WFdbpass,
            $config->WFdbname,
            null,
            $config->WFdbport
        );
    }

    /**
     * @param string $backupFile
     *
     * @return string
     */
    public function getMySQLFileName($backupFile)
    {
        $asFile = explode('.', $backupFile);
        array_pop($asFile);

        return implode('.', $asFile) . '.sql';
    }

    /**
     * @param Config   $config
     * @param string   $filename
     * @param array    $options
     * @param int|null $minimumSpace
     */
    public function create(Config $config, $filename, $options = [], $minimumSpace = null)
    {
        if (!$this->hasExecutableExtension('zip')) {
            $this->oFileBackup->breakCleanUp();
            $this->logger->writePersistent('Required Zip Module is missing!');
            throw new RuntimeException('Required Zip Module is missing!');
        }

        // create mysql dump
        $userPath = $config->WFuserdata;

        if ($sTmpDir = $this->oFileBackup->begin($userPath)) {
            $ssid = array_key_exists('ssid', $options) ? $options['ssid'] : null;
            $this->logger->write('--BEGIN--');

            // DELETE OLD BACKUPS ON THE FILESYSTEM (BACKWARDS AS WELL)
            exec(sprintf('cd %s && ls', $this->oFileBackup->getSnapshotsDir()), $asResult);
            if (is_array($asResult) && count($asResult) > 0) {
                foreach ($asResult as $file) {
                    if (!empty($file) && $file !== $filename) {
                        $fullFile = sprintf('%s%s', $this->oFileBackup->getSnapshotsDir(), $file);
                        if (file_exists($fullFile)) {
                            @unlink($fullFile);
                        }
                    }
                }
            }

            if (null !== $ssid) {
                $this->logger->write($ssid, null, static::SESSION_FILE, false, false);
            }
            $sMySQLFile = $this->getMySQLFileName($filename);
            $sMySQLFullPath = $sTmpDir . $sMySQLFile;
            $this->logger->write('Create MySQL Dump');

            if ($this->hasEnoughFreeDisk($userPath, $minimumSpace) === false) {
                $this->oFileBackup->breakCleanUp();
                $this->logger->write('ERROR');
                $this->logger->writePersistent('Not enough free disk space for Dump creation');
                throw new RuntimeException('Not enough free disk space for Dump creation');
            }

            $this->oDbBackup->createDump($this->convertLegacyDbConf($config), $sMySQLFullPath, null);
            if (filesize($sMySQLFullPath . '.gz') > 1024) {
                $this->logger->write('Create Dump meta file');
                // $this->addMetadata($this->oDbBackup->getMetaFileName($this->oFileBackup->getLocalPath($filename,
                // $userPath)));
            }

            if ($this->hasEnoughFreeDisk($userPath, $minimumSpace) === false) {
                $this->oFileBackup->breakCleanUp();
                $this->logger->write('ERROR');
                $this->logger->writePersistent(sprintf('Not enough free disk space for %s', $userPath));
                throw new RuntimeException(sprintf('Not enough free disk space for %s', $userPath));
            }

            // Create File Backup
            $this->logger->write('Create File Backup for userdata');
            $this->oFileBackup->createBackup($filename, $userPath, $sMySQLFile . '.gz');
            if (filesize($this->getArchivePath($filename, $userPath)) > 1024) {
                $this->logger->write('Add in Backup table');
                // DELETE OLD Backup
                if ($latest = $this->gateway->getLatestBackup()) {
                    $this->db->perform('DELETE FROM backup WHERE id=:id', ['id' => $latest['id']]);
                }
                $this->db->perform('INSERT INTO backup (adresse, name, dateiname, datum) VALUES (:addr,:name,:file_name,NOW())',
                    ['addr' => $options['addr'], 'name' => $options['name'], 'file_name' => $filename]);
            }

            $this->logger->write('--END--');
            $this->removeLoggerFiles();
        }
    }

    /**
     * @param Config $config
     * @param string $filename
     * @param array  $options
     */
    public function restore(Config $config, $filename, $options = [])
    {
        $userPath = $config->WFuserdata;
        if ($sTmpDir = $this->oFileBackup->begin($userPath)) {
            $ssid = array_key_exists('ssid', $options) ? $options['ssid'] : null;

            $this->logger->write('--BEGIN--');
            if (null !== $ssid) {
                $this->logger->write($ssid, null, static::SESSION_FILE, false, false);
            }

            if ($this->hasEnoughFreeDisk($userPath) === false) {
                $this->oFileBackup->breakCleanUp();
                $this->logger->write('ERROR');
                $this->logger->writePersistent('Not enough free disk space for Backup restore');
                throw new RuntimeException('Not enough free disk space for Backup restore');
            }

            // Replay SQL DUMP
            // Backup-Tabelle extra sichern
            $sBackupTmpFullPath = $sTmpDir . 'backup_temp.sql';
            $this->logger->write('DUMP backup table');
            $this->oDbBackup->createDump($this->convertLegacyDbConf($config), $sBackupTmpFullPath, 'backup');
            $sMySQLFile = $this->getMySQLFileName($filename) . '.gz';

            $FullBckPath = $this->getArchivePath($filename, $userPath);
            $oZip = new ZipArchive;
            $xRes = $oZip->open($FullBckPath);
            $this->logger->write('Fetch Database DUMP from Backup archive');
            if ($xRes !== true) {
                $this->oFileBackup->breakCleanUp();
                $this->logger->write('ERROR');
                $this->logger->writePersistent(sprintf('Backup File "%s" cannot be unzipped!', $FullBckPath));
                throw new RuntimeException(sprintf('Backup File "%s" cannot be unzipped!', $FullBckPath));
            }

            if ($oZip->extractTo($sTmpDir, [$sMySQLFile]) === false) {
                $this->oFileBackup->breakCleanUp();
                $this->logger->write('ERROR');
                $this->logger->writePersistent(sprintf('SQL file not found in achieve file %s', $filename));
                throw new RuntimeException(sprintf('SQL file not found in achieve file %s', $filename));
            }

            $oZip->close();

            $asTables = $this->gateway->getTables();
            $this->logger->write('DROP ALL TABLES');
            foreach ($asTables as $sTable) {
                $this->db->perform('DROP TABLE IF EXISTS ' . $sTable);
            }
            $sMySQLFullPath = $sTmpDir . $sMySQLFile;
            $this->logger->write('RESTORE Database DUMP');
            $this->oDbBackup->restoreDump($this->convertLegacyDbConf($config), $sMySQLFullPath);
            // remove Backup Dump
            $this->logger->write('REMOVE DUMP FILE');
            @unlink($sMySQLFullPath);

            // RESTORE Backup table
            $this->logger->write('Restore Backup Table');
            $this->oDbBackup->restoreDump($this->convertLegacyDbConf($config), $sBackupTmpFullPath . '.gz');
            @unlink($sBackupTmpFullPath . '.gz');

            $this->logger->write('Restore Backup System files');

            $restoreOptions = [];
            if (array_key_exists('exclude_dir', $options) && is_array($options['exclude_dir'])) {
                $restoreOptions['exclude_dir'] = $options['exclude_dir'];
            }
            $this->oFileBackup->restoreFileSystem($filename, $userPath, $restoreOptions);
            $iUserId = array_key_exists('user_id', $options) ? $options['user_id'] : 0;
            if (!empty($iUserId)) {
                $ssid = array_key_exists('ssid', $options) ? $options['ssid'] : null;
                $ip = array_key_exists('ip', $options) ? $options['ip'] : null;
                $this->logger->write('RECONNECT current User');
                $this->reconnectUser($iUserId, $ssid, $ip);
            }
            if (array_key_exists('old_dbname', $options) && !empty($options['old_dbname'])) {
                $this->migratePublicSubdirectory($options['old_dbname'], $config);
            }
            $this->logger->write('--END--');
        }
    }

    /**
     * @return string
     */
    public function getArchiveExtension()
    {
        return $this->oFileBackup->getBackupExtension();
    }

    /**
     * @param string $filename
     *
     * @param string $userPath
     *
     * @return string
     */
    public function getArchivePath($filename, $userPath)
    {
        return $this->oFileBackup->getLocalPath($filename, $userPath);
    }

    /**
     * @param int         $iUserId
     *
     * @param string      $ssid
     *
     * @param string|null $ip
     *
     * @return void
     */
    public function reconnectUser($iUserId, $ssid, $ip = null)
    {
        if (isset($iUserId) && is_numeric($iUserId) && is_string($ssid)) {
            $ip = null === $ip ? '127.0.0.1' : $ip;
            $this->db->perform('DELETE FROM useronline WHERE user_id=:uid', ['uid' => $iUserId]);
            $this->db->perform(
                'INSERT INTO useronline (user_id, login, sessionid, ip, time) VALUES (:uid,1,:ssid,:ip,NOW())',
                [
                    'uid'  => $iUserId,
                    'ssid' => $ssid,
                    'ip'   => $ip,
                ]
            );
        }
    }

    /**
     * @param string      $filename
     *
     * @param string|null $userPath
     *
     * @return string|null
     */
    public function getDumpMetaData($filename, $userPath = null)
    {
        $filePath = $this->oFileBackup->getLocalPath($filename, $userPath, true);

        return $this->oDbBackup->getDumpMetaData($filePath);
    }

    /**
     * @param string $filename
     * @param string $userPath
     *
     * @return array
     */
    public function checkSumOnAfterRecovery($filename, $userPath = null)
    {
        $asDiff = [];
        $hDbCheckSums = $this->oDbBackup->excludeCheckSumTables($this->gateway->getTablesChecksum());
        if (($xData = $this->getDumpMetaData($filename, $userPath)) && !empty($hFileCheckSums = $xData['tables'])) {
            $ahFileCheckSums = $this->oDbBackup->excludeCheckSumTables($hFileCheckSums);
            foreach ($ahFileCheckSums as $table => &$asFileCheckSum) {
                // downward compatible
                if (!is_array($asFileCheckSum)) {
                    $params = ['checksum', 'items'];
                    $values = [$asFileCheckSum, 0];
                    $asFileCheckSum = array_combine($params, $values);
                }
                if ($asFileCheckSum['checksum'] !== $hDbCheckSums[$table]['checksum'] && $asFileCheckSum['items'] !== $hDbCheckSums[$table]['items']) {
                    $asDiff[] = $table;
                }
            }

            return $asDiff;
        }

        return null;
    }

    /**
     * @param string      $xConfig     Configuration options for backup
     * @param string      $identifier  description/Title of cron action
     * @param string      $sParam      Parameter to set in the configuration table for that action
     * @param string      $cronFile    Cron file (in .php) located under cronjobs directory
     * @param string|null $userDataDir userData directory
     *
     * @throws RuntimeException
     * @throws Exception
     * @return bool
     */
    public function addToProcessStarter(
        $xConfig,
        $identifier = 'Backup',
        $sParam = 'backup_configuration_cron',
        $cronFile = 'backup',
        $userDataDir = null
    ) {
        // check if backup or restore is running?
        if ($this->oDbBackup->getLockStatus() === AdapterInterface::STATUS_WORKING ||
            $this->oFileBackup->getLockStatus($userDataDir) === FileBackup::STATUS_WORKING) {
            return false;
        }

        $date = new DateTime();
        $yesterday = $date->sub(new DateInterval('P1D'));

        try {
            $fakeLastRun = $yesterday->format('Y-m-d H:i:s');
            // $this->db->perform('UPDATE prozessstarter SET aktiv=:active WHERE mutex=:mut',
            //    ['active' => 0, 'mut' => 0]);
            // ADD NEW JOB ONLY IF THERE IS NO RUNNING JOB
            try {
                $xCheckPS = $this->processStarter->tryCheckProcess($cronFile, 1000, $date, $identifier);
            } catch (BackupProcessStarterException $exception) {
                $this->logger->writePersistent($exception->getMessage());
                throw new RuntimeException($exception->getMessage());
            }

            $this->backupSystemConfiguration->trySetConfiguration($sParam, $xConfig);
            if ($xCheckPS === false) {
                $aiAffected = $this->db->fetchAffected(
                    'UPDATE prozessstarter SET aktiv=:active, letzteausfuerhung=:timestamp,
                          status=:status WHERE parameter=:cron_file',
                    ['active' => 1, 'timestamp' => $fakeLastRun, 'cron_file' => $cronFile, 'status' => '']
                );

                return !empty($aiAffected);
            }
        } catch (DatabaseExceptionInterface $exception) {
            $this->logger->writePersistent($exception->getMessage());
            throw new RuntimeException($exception->getMessage());
        }

        return true;
    }

    /**
     * @param null|string $fileName
     *
     * @return void
     */
    public function removeLoggerFiles($fileName = null)
    {
        if (null !== $fileName && is_file($fileName)) {
            $tmpBackup = $fileName;
            $fileExploded = explode('.', $fileName);
            $extension = array_pop($fileExploded);
            if ($extension === 'zip') {
                $tmpMeta = implode('.', $fileExploded) . '.meta';
                if (is_file($tmpMeta)) {
                    unlink($tmpMeta);
                }
            }
            unlink($tmpBackup);
        }
        $this->logger->delete();
        $this->logger->delete(null, static::SESSION_FILE);
    }

    /**
     * @param string   $userData
     * @param int|null $minFreeDisk
     *
     * @return bool
     */
    private function hasEnoughFreeDisk($userData, $minFreeDisk = null)
    {
        $rootPath = $this->getRootPathByUserDataPath($userData);
        $minFreeDiskAverage = empty($minFreeDisk) || $minFreeDisk < static::MIN_FREE_DISK
            ? static::MIN_FREE_DISK : $minFreeDisk;
        $free = disk_free_space($rootPath);
        $minFree = (int)$minFreeDiskAverage;

        return ($free > 0 && $free > $minFree);
    }

    /**
     * @param string $userData
     *
     * @return string
     */
    private function getRootPathByUserDataPath($userData)
    {
        if (empty($userData)) {
            $this->oFileBackup->breakCleanUp();
            $this->logger->write('ERROR');
            $this->logger->writePersistent(sprintf('UserData Dir "%s" is missing!', $userData));
            throw new RuntimeException(sprintf('UserData Dir "%s" is missing!', $userData));
        }

        return dirname($userData);
    }

    /**
     * @return bool
     */
    public function isInLoginLockMode()
    {
        if ($this->backupSystemConfiguration->getConfiguration('login_lock_mode') === '1') {
            $timeMaintenance = (int)$this->backupSystemConfiguration->getConfiguration('login_lock_mode_time');

            if (empty($timeMaintenance)) {
                $this->backupSystemConfiguration->trySetConfiguration('login_lock_mode_time', time());

                return true;
            }

            $timeOutMaintenance = (int)$this->configurationService->getConfiguration('login_lock_mode_timeout');
            // default 10min
            $timeOut = empty($timeOutMaintenance) ? 600 : $timeOutMaintenance;

            if (time() - $timeMaintenance < $timeOut) {
                return true;
            }

            $this->backupSystemConfiguration->trySetConfiguration('login_lock_mode', 0);
            $this->backupSystemConfiguration->trySetConfiguration('login_lock_mode_time', '0');
            $this->backupSystemConfiguration->trySetConfiguration('login_lock_mode_timeout', '0');
        }

        return false;
    }

    /**
     * @param string $old_dbname
     * @param Config $config
     *
     * @return void
     */
    protected function migratePublicSubdirectory($old_dbname, Config $config)
    {
        $dbName = $config->WFdbname;
        if ($old_dbname !== $dbName) {
            $userPath = $config->WFuserdata;
            $userPath = rtrim($userPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            foreach (self::$publicSubdirectories as $subdirectory) {
                $oldDbPath = $userPath . $subdirectory . DIRECTORY_SEPARATOR . $old_dbname;
                $newDbPath = $userPath . $subdirectory . DIRECTORY_SEPARATOR . $dbName;
                if (is_dir($userPath . $old_dbname) && !is_dir($userPath . $subdirectory)) {
                    $cmd = 'mv %s %s';
                    @exec(sprintf($cmd, $oldDbPath, $newDbPath));
                }
            }
        }
    }

    /**
     * @param string $name
     *
     * @throws RuntimeException
     * @return bool
     */
    public function hasExecutableExtension($name)
    {
        if (!function_exists('exec')) {
            $this->logger->writePersistent('Required Function exec is missing');
            throw new RuntimeException('Required Function exec is missing');
        }
        if (!is_string($name)) {
            return false;
        }
        exec(sprintf('whereis %s', $name), $out);
        if (empty($out)) {
            return false;
        }
        $result = $out[0];
        $resultExploded = explode(':', $result);
        array_shift($resultExploded);

        return !empty(trim(implode('', $resultExploded)));
    }
}
