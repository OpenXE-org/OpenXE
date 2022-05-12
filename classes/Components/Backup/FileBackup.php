<?php

namespace Xentral\Components\Backup;

use PHPUnit\Runner\Exception;
use Xentral\Components\Backup\Logger\BackupLog;
use Xentral\Components\Backup\Exception\BackupException;
use ZipArchive;

final class FileBackup implements FileBackupInterface
{

    /** @var string backup path */
    private $sUserPath;

    /** @var BackupLog $logger */
    private $logger;

    /** @var string $cacheTmp */
    private $cacheTmp;

    /**
     * @param BackupLog $logger
     * @param string    $cacheTmp
     */
    public function __construct(BackupLog $logger, $cacheTmp)
    {
        $this->logger = $logger;
        $this->cacheTmp = $cacheTmp;
    }


    /**
     * @return string
     */
    protected function getMainPath()
    {
        $asPath = explode(DIRECTORY_SEPARATOR, $this->sUserPath);
        array_pop($asPath);

        return implode(DIRECTORY_SEPARATOR, $asPath) . DIRECTORY_SEPARATOR;
    }

    /**
     * returns the full file name path
     *
     * @param string $filename
     * @param bool   $bIsSnapshots
     * @param null   $userPath
     *
     * @throws BackupException
     * @return string
     */
    public function getLocalPath($filename, $userPath = null, $bIsSnapshots = true)
    {
        if (null !== $userPath) {
            $this->sUserPath = $userPath;
        }
        $path = $this->getMainPath();
        if ($bIsSnapshots === true) {
            $path .= FileBackupInterface::SNAPSHOTS_FOLDER . DIRECTORY_SEPARATOR;
        }
        if (!file_exists($path) && !@mkdir($path) && !is_dir($path)) {
            $this->logger->writePersistent(sprintf('Directory "%s" was not created', $path));
            throw new BackupException(sprintf('Directory "%s" was not created', $path));
        }

        return $path . $filename;
    }

    /**
     * @return string
     */
    private function tmpDir()
    {
        return $this->getMainPath() . 'backup/.backup' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getSnapshotsDir()
    {
        return $this->getMainPath() . FileBackupInterface::SNAPSHOTS_FOLDER . DIRECTORY_SEPARATOR;
    }

    /**
     * @param $path
     *
     * @return false|int
     */
    protected function addLock($path)
    {
        return $this->logger->write(time(), $path, FileBackupInterface::PID_FILE, false, false);
    }

    /**
     * @return bool
     */
    private function tryPurgePidFile()
    {
        $pidFile = $this->tmpDir() . FileBackupInterface::PID_FILE;

        $time = file_get_contents($pidFile);

        if ((time() - (int)$time > FileBackupInterface::TIME_OUT)) {
            return unlink($pidFile);
        }

        return false;
    }

    /**
     * @param string|null $userPath
     *
     * @throws BackupException
     * @return string|null
     */
    public function begin($userPath = null)
    {
        if (null !== $userPath) {
            $this->sUserPath = $userPath;
        }
        $path = $this->tmpDir();

        if (is_dir($path)) {
            @exec('rm -rf ' . $path);
        }

        $backupDir = $this->getMainPath() . 'backup';
        if (file_exists($backupDir . DIRECTORY_SEPARATOR . 'status.txt')) {
            @unlink($backupDir . DIRECTORY_SEPARATOR . 'status.txt');
        }

        if (file_exists($backupDir . DIRECTORY_SEPARATOR . 'session.txt')) {
            @unlink($backupDir . DIRECTORY_SEPARATOR . 'session.txt');
        }

        if (!file_exists($path) && !@mkdir($path, 0777, true) && !is_dir($path)) {
            $this->logger->writePersistent(sprintf('Directory "%s" was not created', $path));
            throw new BackupException(sprintf('Directory "%s" was not created', $path));
        }

        if ($this->getLockStatus() === FileBackupInterface::STATUS_WORKING && $this->tryPurgePidFile() === false) {
            return null;
            //throw new BackupException(sprintf('Backup is Running'));
        }

        if ($this->addLock($path) === false) {
            $this->logger->writePersistent('Failed start backup');
            throw new BackupException('Failed start backup');
        }

        return $path;
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    protected function cleanUp($file)
    {
        $path = $this->tmpDir();
        if ($this->moveDir($path . $file, $this->getLocalPath($file)) === true) {
            return $this->deleteDir($path);
        }
        $this->logger->writePersistent(sprintf('Clean Up of %s failed', $path));
        throw new BackupException(sprintf('Clean Up of %s failed', $path));
    }

    /**
     * @param string $class_name
     *
     * @return bool
     */
    protected function classExists($class_name)
    {
        return class_exists($class_name);
    }

    /**
     * @param ZipArchive $oZip
     * @param string     $fileName
     * @param int        $flags
     *
     * @return mixed
     */
    protected function openZipObject($oZip, $fileName, $flags = 0)
    {
        return $oZip->open($fileName, $flags);
    }

    /**
     * @param string $oldDir
     * @param string $newDir
     *
     * @return bool
     */
    protected function moveDir($oldDir, $newDir)
    {
        return @rename($oldDir, $newDir);
    }

    /**
     * @param string $dir
     *
     * @return bool
     */
    protected function isDir($dir)
    {
        return @mkdir($dir) || is_dir($dir);
    }

    /**
     * @return string
     */
    public function getBackupExtension()
    {
        return FileBackupInterface::COMPRESS_EXTENSION;
    }

    /**
     * @param string      $filename   Zipped file name
     * @param string      $userPath   local directory to backup
     * @param string|null $sMySQLFile MySQL Backup file
     *
     * @return bool
     */
    public function createBackup($filename, $userPath, $sMySQLFile = null)
    {
        $this->sUserPath = $userPath;
        $rootPath = realpath($userPath);

        if (!file_exists($userPath)) {
            $this->logger->writePersistent(sprintf('Directory "%s" was not found', $userPath));
            throw new BackupException(sprintf('Directory "%s" was not found', $userPath));
        }

        $tmpFilename = $this->tmpDir() . $filename;
        $sMySQLFullPath = $this->tmpDir() . $sMySQLFile;

        if (null !== $sMySQLFile && is_file($sMySQLFullPath) && filesize($sMySQLFullPath) > 1024) {
            $this->logger->write('Add MySQL file to Zip');
            exec('cd ' . $rootPath . ' && mv ' . $sMySQLFullPath . ' ' . $sMySQLFile);
        }

        exec('cd ' . $rootPath . ' && zip -r -9 ' . $tmpFilename . ' * .[^.]* -x "wiki/*"');

        if (null !== $sMySQLFile) {
            exec('cd ' . $rootPath . ' && rm -f ' . $sMySQLFile);
        }

        return $this->cleanUp($filename);
    }

    /**
     * @param string $dirPath
     *
     * @return bool
     */
    private function deleteDir($dirPath)
    {
        if (is_dir($dirPath)) {
            if (substr($dirPath, strlen($dirPath) - 1, 1) !== '/') {
                $dirPath .= '/';
            }
            $files = glob($dirPath . '*', GLOB_MARK);
            foreach ($files as $file) {
                if (is_dir($file)) {
                    $this->deleteDir($file);
                } else {
                    unlink($file);
                }
            }

            return rmdir($dirPath);
        }
        $this->logger->writePersistent(sprintf('Deleted DIR %s failed', $dirPath));
        throw new BackupException(sprintf('Deleted DIR %s failed', $dirPath));
    }

    /**
     * @param string $backupFile
     * @param string $userPath
     * @param array  $options
     *
     * @return bool
     */
    public function restoreFileSystem($backupFile, $userPath, $options = [])
    {
        $default = ['template_file_dir' => null, 'exclude_dir' => ['wiki']];
        $options = array_merge($default, $options);
        $templateFileDir = $options['template_file_dir'];
        $this->sUserPath = $userPath;
        $bIsSnapshots = null === $templateFileDir;

        $this->sUserPath = null === $templateFileDir ? $userPath : $templateFileDir;

        if (file_exists($file = $this->getLocalPath($backupFile, null, $bIsSnapshots))) {
            $userDataPath = realpath($userPath);
            $tmpExtract = $this->tmpDir() . FileBackupInterface::LOCAL_FILES_DIR_NAME . 'tmp';
            if (!file_exists($tmpExtract) && !@mkdir($tmpExtract) && !is_dir($tmpExtract)) {
                $this->logger->writePersistent(sprintf('Directory "%s" was not created', $tmpExtract));
                throw new BackupException(sprintf('Directory "%s" was not created', $tmpExtract));
            }

            if (!$this->classExists('ZipArchive')) {
                $this->logger->writePersistent('Class ZipArchive is missing!');
                throw new BackupException('Class ZipArchive is missing!');
            }

            $oZip = new ZipArchive();

            if ($this->openZipObject($oZip, $file, ZipArchive::CHECKCONS) !== true) {
                $this->logger->writePersistent(sprintf('Failure to open file in "%s"', $file));
                throw new BackupException(sprintf('Failure to open file in "%s"', $file));
            }

            $oZip->extractTo($tmpExtract);
            $oZip->close();

            // move user data
            $shortTmp = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '.rmTmp';
            $sBeforeTmp = $shortTmp . uniqid('', true) . 'before';

            if (!$this->isDir($sBeforeTmp)) {
                $this->logger->writePersistent(sprintf('Directory "%s" was not created', $sBeforeTmp));
                throw new BackupException(sprintf('Directory "%s" was not created', $sBeforeTmp));
            }

            $this->logger->write('Moving userdata away');

            if (!$this->moveDir($userDataPath, $sBeforeTmp)) {
                $this->logger->writePersistent(sprintf('Moving %s into %s failed! ', $userDataPath, $sBeforeTmp));
                throw new BackupException(sprintf('Moving %s into %s failed! ', $userDataPath, $sBeforeTmp));
            }

            if (array_key_exists('exclude_dir', $options) && is_array($options['exclude_dir'])) {
                $this->excludeDirectory($options['exclude_dir'], $tmpExtract, $sBeforeTmp);
            }

            $this->logger->write('Recovering userData');
            if (!$this->moveDir($tmpExtract, $userDataPath)) {
                $this->logger->writePersistent(sprintf('Moving %s into %s failed!', $tmpExtract, $userDataPath));
                throw new BackupException(sprintf('Moving %s into %s failed!', $tmpExtract, $userDataPath));
            }

            // FIX TMP ISSUE
            if (!empty($this->cacheTmp) && is_dir($this->cacheTmp)) {
                $this->logger->write('Delete DB tmp');
                $this->deleteDir($this->cacheTmp);
            }

            // remove DB if exists
            $backupFileExploded = explode('.', $backupFile);
            array_pop($backupFileExploded);
            $tmpSql = implode('.', $backupFileExploded) . '.sql.gz';
            if (is_file($userDataPath . DIRECTORY_SEPARATOR . $tmpSql)) {
                exec('cd ' . $userDataPath . ' && rm -f ' . $tmpSql);
            }

            return $this->deleteDir($this->tmpDir()) && $this->deleteDir($sBeforeTmp);
        }

        return false;
    }

    /**
     * @param array  $excludeDir
     * @param string $tmpDir extracted temporally directory
     * @param string $oldUserDataDir
     */
    protected function excludeDirectory($excludeDir = [], $tmpDir, $oldUserDataDir)
    {
        foreach ($excludeDir as $directory) {
            // EXCLUDE WIKI DIRECTORY
            $keepPath = $tmpDir . DIRECTORY_SEPARATOR . $directory;
            $keepDirTmp = $oldUserDataDir . DIRECTORY_SEPARATOR . $directory;

            if (!is_dir($keepPath) && $directory !== 'wiki') {
                $this->logger->writePersistent(sprintf('Directory "%s" cannot be skipped', $keepPath));
                throw new BackupException(sprintf('Directory "%s" cannot be skipped', $keepPath));
            }

            if ($directory !== 'wiki') {
                $this->deleteDir($keepPath);
            }

            if (file_exists($keepDirTmp)) {
                $oldDirTmp = rtrim(
                        sys_get_temp_dir(),
                        DIRECTORY_SEPARATOR
                    ) . DIRECTORY_SEPARATOR . '.' . $directory . 'Tmp' . uniqid('', true);
                if (!$this->isDir($oldDirTmp)) {
                    $this->logger->writePersistent(sprintf('Directory "%s" was not created', $oldDirTmp));
                    throw new BackupException(sprintf('Directory "%s" was not created', $oldDirTmp));
                }
                $oldDir = $oldDirTmp . DIRECTORY_SEPARATOR . $directory;
                if (!$this->moveDir($keepDirTmp, $oldDir)) {
                    $this->logger->writePersistent(sprintf('Could not move %s directory into "%s"', $directory,
                        $oldDir));
                    throw new BackupException(sprintf('Could not move %s directory into "%s"', $directory,
                        $oldDir));
                }
            }

            // Reset Latest WIKI Directory
            if (isset($oldDir) && is_dir($oldDir)) {
                $this->logger->write('Reset Wiki Directory');

                if (!$this->moveDir($oldDir, $keepPath)) {
                    $this->logger->writePersistent(sprintf('Could not move %s directory into "%s"', $oldDir,
                        $keepPath));
                    throw new BackupException(sprintf('Could not move %s directory into "%s"', $oldDir, $keepPath));
                }
            }
        }
    }

    /**
     * @param string|null $userDataDir
     *
     * @return string
     */
    public function getLockStatus($userDataDir = null)
    {
        if (null !== $userDataDir) {
            $this->sUserPath = $userDataDir;
        }
        $path = $this->tmpDir();
        if (file_exists($path . FileBackupInterface::PID_FILE) &&
            ($time = file_get_contents($path . FileBackupInterface::PID_FILE)) &&
            (time() - (int)$time < FileBackupInterface::TIME_OUT)
        ) {
            return FileBackupInterface::STATUS_WORKING;
        }

        return FileBackupInterface::STATUS_WAITING;
    }

    /**
     * Clean everything without files move. This might be used, when breaking started backup job
     *
     * @return bool
     */
    public function breakCleanUp()
    {
        return $this->deleteDir($this->tmpDir());
    }
}
