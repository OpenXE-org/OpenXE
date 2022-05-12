<?php

namespace Xentral\Modules\SystemTemplates;

use Config;
use Xentral\Components\Backup\DatabaseBackup;
use Xentral\Components\Backup\FileBackup;
use Xentral\Components\Backup\Logger\BackupLog;
use Xentral\Components\Database\Exception\DatabaseExceptionInterface;
use Xentral\Components\Database\Database;
use Xentral\Modules\Backup\BackupGateway;
use Xentral\Modules\Backup\BackupService;
use Xentral\Modules\SystemTemplates\Exception\InvalidArgumentException;
use Xentral\Modules\SystemTemplates\Exception\RuntimeException;
use Xentral\Modules\SystemTemplates\Validator\Exception\SystemTemplateValidatorException;
use Xentral\Modules\SystemTemplates\Validator\SystemTemplateValidator;
use Xentral\Modules\Backup\Exception\RuntimeException As BackupModuleRuntimeException;
use ZipArchive;
use \Exception;

final class SystemTemplatesService
{
    /** @var BackupGateway $gateway */
    private $gateway;

    /** @var string file */
    const META_FILE = 'meta.json';

    /** @var Database $db */
    private $db;

    /** @var FileBackup $oFileBackup */
    private $oFileBackup;

    /** @var DatabaseBackup $oDbBackup */
    private $oDbBackup;

    /** @var BackupLog $logger */
    private $logger;

    /** @var string $templateFilePath */
    private $templateFilePath;

    /** @var SystemTemplateValidator $validator */
    private $validator;

    /** @var BackupService $backupService */
    private $backupService;

    /**
     * SystemTemplatesService constructor.
     *
     * @param SystemTemplatesGateway  $gateway
     * @param DatabaseBackup          $oDbBackup
     * @param FileBackup              $oFileBackup
     * @param BackupService           $backupService
     * @param Database                $database
     * @param SystemTemplateValidator $validator
     * @param BackupLog               $logger
     * @param string                  $templateFilePath
     */
    public function __construct(
        SystemTemplatesGateway $gateway,
        DatabaseBackup $oDbBackup,
        FileBackup $oFileBackup,
        BackupService $backupService,
        Database $database,
        SystemTemplateValidator $validator,
        BackupLog $logger,
        string $templateFilePath
    ) {
        $this->gateway = $gateway;
        $this->db = $database;
        $this->oFileBackup = $oFileBackup;
        $this->oDbBackup = $oDbBackup;
        $this->logger = $logger;
        $this->templateFilePath = $templateFilePath;
        $this->validator = $validator;
        $this->backupService = $backupService;
    }

    /**
     * @param array $templates
     *
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    public function importTemplates(array $templates = []): bool
    {

        if (empty($templates)) {
            throw new InvalidArgumentException('Templates data are missing!');
        }

        $this->db->beginTransaction();
        $this->db->perform('DELETE FROM `systemtemplates`');

        try {
            foreach ($templates as $template) {
                $this->addTemplate($template);
            }
        } catch (DatabaseExceptionInterface $exception) {
            $this->db->rollBack();

            return false;
        } catch (InvalidArgumentException $exception) {
            $this->db->rollBack();

            return false;
        }

        $this->db->commit();

        return true;

    }

    /**
     * @param array $template
     *
     * @throws InvalidArgumentException
     */
    public function addTemplate(array $template = []): void
    {
        if (empty($template)) {
            throw new InvalidArgumentException('Template cannot be empty');
        }
        if (!is_array($template)) {
            throw new InvalidArgumentException('Template should be an Array');
        }

        try {
            if (!$this->validator->fromMeta($template)->isValid()) {
                throw new InvalidArgumentException(json_encode($this->validator->getErrors()));
            }
        } catch (SystemTemplateValidatorException $exception) {
            throw new InvalidArgumentException($exception->getMessage());
        }

        $sql = 'INSERT INTO systemtemplates (filename, category, title, description) 
                        VALUES (:filename, :category, :title, :description)';
        $this->db->perform($sql, $template);
    }

    /**
     * @return string
     */
    protected function getTemplatesDir(): string
    {
        return $this->templateFilePath;
    }

    /**
     * @param string|null $key
     *
     * @throws InvalidArgumentException | RuntimeException
     * @return mixed|null
     */
    public function getMetaContent(?string $key = null)
    {
        if (!file_exists($sMetaFile = $this->getTemplatesDir() . static::META_FILE)) {
            throw new InvalidArgumentException(sprintf('Cannot find meta file'));
        }

        if (empty($sJsonContent = file_get_contents($sMetaFile))) {
            throw new RuntimeException('Meta content cannot be read');
        }

        if (($xData = json_decode($sJsonContent, true)) !== null && (json_last_error() === JSON_ERROR_NONE)) {

            if ($key !== null && array_key_exists($key, $xData)) {
                return $xData[$key];
            }

            return $xData;
        }
        throw new RuntimeException('Reading Meta data failed');
    }

    /**
     * @return bool|false|string
     */
    public function getMetaFileCheckSum()
    {
        if (file_exists($sMetaFile = $this->getTemplatesDir() . static::META_FILE)) {
            return md5_file($sMetaFile);
        }

        return false;
    }

    /**
     * @param string $fileName
     *
     * @throws RuntimeException
     *
     * @return string
     */
    public function getFullFilePath(string $fileName): string
    {
        if (!empty($fileName)) {
            return $this->getTemplatesDir() . $fileName;
        }
        throw new RuntimeException('Filename is missing');
    }

    /**
     * @param Config $config
     * @param string $filename
     * @param array  $options
     */
    public function install(Config $config, string $filename, array $options = []): void
    {

        if ($sTmpDir = $this->oFileBackup->begin($this->getTemplatesDir())) {
            $ssid = array_key_exists('ssid', $options) ? $options['ssid'] : null;

            $this->logger->write('--BEGIN--');
            if (null !== $ssid) {
                $this->logger->write($ssid, null, BackupService::SESSION_FILE, false, false);
            }
            $sBackupTmpFullPath = $sTmpDir . 'backup_temp.sql';
            $this->logger->write('DUMP backup table');
            $this->oDbBackup->createDump($this->backupService->convertLegacyDbConf($config), $sBackupTmpFullPath,
                'backup');
            $sMySQLFile = $this->backupService->getMySQLFileName($filename) . '.gz';

            $FullBckPath = $this->getFullFilePath($filename);
            $oZip = new ZipArchive;
            $xRes = $oZip->open($FullBckPath);
            $this->logger->write('Fetch Database DUMP from template archive');
            if ($xRes !== true) {
                throw new RuntimeException(sprintf('Database Dump not found in %s', $FullBckPath));
            }
            $oZip->extractTo($sTmpDir, [$sMySQLFile]);
            $oZip->close();
            $licenseData = $this->db->fetchRow(
                'SELECT `lizenz`, `schluessel` FROM `firmendaten` ORDER BY `id` DESC LIMIT 1'
            );
            $asTables = $this->gateway->getTables();
            $this->logger->write('DROP ALL TABLES');
            foreach ($asTables as $sTable) {
                $this->db->perform('DROP TABLE IF EXISTS ' . $sTable);
            }
            $sMySQLFullPath = $sTmpDir . $sMySQLFile;
            $this->logger->write('RESTORE Template Databases');
            $this->oDbBackup->restoreDump($this->backupService->convertLegacyDbConf($config), $sMySQLFullPath);
            // remove Backup Dump
            $this->logger->write('REMOVE DUMP FILE');
            @unlink($sMySQLFullPath);

            // RESTORE Backup table
            $this->logger->write('RESTORE Backup table');
            $this->oDbBackup->restoreDump($this->backupService->convertLegacyDbConf($config),
                $sBackupTmpFullPath . '.gz');
            @unlink($sBackupTmpFullPath . '.gz');
            if (!empty($licenseData)) {
                $lastId = $this->db->fetchCol('SELECT MAX(`id`) FROM `firmendaten`');
                $this->db->perform(
                    'UPDATE `firmendaten` SET `lizenz` = :license, `schluessel` = :authkey WHERE `id` = :id',
                    ['license' => $licenseData['lizenz'], 'authkey' => $licenseData['schluessel'], 'id' => $lastId]
                );
            }
            $this->logger->write('RESTORE Local Template files ');

            $restoreOptions = ['template_file_dir' => $this->getTemplatesDir()];
            if (array_key_exists('exclude_dir', $options) && is_array($options['exclude_dir'])) {
                $restoreOptions['exclude_dir'] = $options['exclude_dir'];
            }
            foreach(['dms', 'pdfarchiv', 'pdfmirror', 'emailbackup', 'uebertragung'] as $subDirectory) {
                @exec('rm -Rf '.$config->WFuserdata.$subDirectory.'/'.$config->WFdbname);
            }

            $this->oFileBackup->restoreFileSystem($filename, $config->WFuserdata, $restoreOptions);
            if (array_key_exists('user_id', $options)) {
                $iUserId = $options['user_id'];
                $ssid = array_key_exists('ssid', $options) ? $options['ssid'] : null;
                $ip = array_key_exists('ip', $options) ? $options['ip'] : null;
                $this->logger->write('RECONNECT current User');
                $this->backupService->reconnectUser($iUserId, $ssid, $ip);
            }
            @unlink($config->WFuserdata.'/cronjobkey.txt');
            $this->logger->write('--END--');
        }
    }

    /**
     * @param string $templateFileName
     *
     * @return array
     */
    public function checkSumOnAfterInstall(string $templateFileName): ?array
    {
        $hDbCheckSums = $this->gateway->getTablesChecksum();
        $metaFileName = $this->oDbBackup->getMetaFileName($templateFileName);
        $metaPath = $this->getTemplatesDir() . $metaFileName;
        if (($xData = $this->oDbBackup->getMetaInfo($metaPath)) && !empty($hFileCheckSums = $xData['tables'])) {
            $hDiff = array_diff_assoc($this->oDbBackup->excludeCheckSumTables($hDbCheckSums),
                $this->oDbBackup->excludeCheckSumTables($hFileCheckSums));

            return !empty($hDiff) ? array_keys($hDiff) : [];
        }

        return null;
    }

    /**
     * @param string      $filename
     * @param string|null $userPath
     *
     * @return mixed|null
     */
    public function getDumpMetaData(string $filename, ?string $userPath = null)
    {
        $userPath = null === $userPath ? $this->getTemplatesDir() : $userPath;
        $filePath = $this->oFileBackup->getLocalPath($filename, $userPath, false);

        return $this->oDbBackup->getDumpMetaData($filePath);
    }

    /**
     * @param string $xConfig
     * @param string $identifier
     * @param string $sParam
     * @param string $cronFile
     *
     * @throws RuntimeException
     * @throws Exception
     *
     * @return bool
     */
    public function addToProcessStarter(
        string $xConfig,
        string $identifier = 'Vorlage/System-Backup',
        string $sParam = 'system_template_configuration_cron',
        string $cronFile = 'system_template'
    ): bool
    {
        try {
            return $this->backupService->addToProcessStarter($xConfig, $identifier, $sParam, $cronFile);
        } catch (BackupModuleRuntimeException $exception) {
            throw new RuntimeException($exception->getMessage());
        }
    }

    /**
     * @return void
     */
    public function removeLoggerFiles(): void
    {
        $this->backupService->removeLoggerFiles();
    }
}
