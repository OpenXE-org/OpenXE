<?php

namespace Xentral\Modules\DemoExporter;

use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use stdClass;
use Throwable;
use Xentral\Components\Backup\Logger\BackupLog;
use Xentral\Components\Database\Database;
use Xentral\Components\Database\Exception\DatabaseExceptionInterface;
use Xentral\Components\Database\Exception\QueryFailureException;
use Xentral\Components\Exporter\Csv\CsvExporter;
use Xentral\Components\Exporter\Exception\ExporterExceptionInterface;
use Xentral\Modules\Backup\BackupService;
use Xentral\Modules\Backup\BackupSystemConfigurationService;
use Xentral\Modules\Backup\Exception\RuntimeException as BackupModuleRuntimeException;
use Xentral\Modules\DemoExporter\Exception\DemoExporterCleanerException;
use Xentral\Modules\DemoExporter\Exception\DemoExporterException;
use ZipArchive;


final class DemoExporterService
{
    const DEMO_EXPORTER_CONFIG_NAME = 'demo_exporter';

    /** @var DemoExporterCleanerService $cleanerService */
    private $cleanerService;

    /** @var string $tmpDir */
    private $tmpDir;

    /**
     * @var BackupSystemConfigurationService
     */
    private $configurationService;
    /** @var array $customDemoExporter */
    private $customDemoExporter;
    /**
     * @var BackupService
     */
    private $backupService;
    /**
     * @var DemoExporterGateway
     */
    private $gateway;
    /**
     * @var Database
     */
    private $db;

    /** @var null|string $sqlTmpArticle */
    private $sqlTmpArticle = null;

    /**
     * @var DemoExporterDateiService
     */
    private $dateiService;

    /** @var null|string $zipFile */
    private $zipFile = null;
    /**
     * @var BackupLog
     */
    private $logger;

    /**
     * DemoExporterService constructor.
     *
     * @param DemoExporterDateiService         $dateiService
     * @param DemoExporterCleanerService       $cleanerService
     * @param Database                         $db
     * @param BackupSystemConfigurationService $configurationService
     * @param BackupService                    $backupService
     * @param DemoExporterGateway              $gateway
     * @param BackupLog                        $logger
     */
    public function __construct(
        DemoExporterDateiService $dateiService,
        DemoExporterCleanerService $cleanerService,
        Database $db,
        BackupSystemConfigurationService $configurationService,
        BackupService $backupService,
        DemoExporterGateway $gateway,
        BackupLog $logger
    ) {
        $this->dateiService = $dateiService;
        $this->cleanerService = $cleanerService;
        $this->tmpDir = $dateiService->getTmpPath();
        $this->db = $db;
        $this->configurationService = $configurationService;
        $this->backupService = $backupService;
        $this->gateway = $gateway;
        $this->logger = $logger;
    }

    /**
     * @param array $options
     *
     * @throws DemoExporterException
     * @return void
     */
    public function setDumpOptions($options = [])
    {
        try {
            $default = [
                'artikel' => 'artikel.geloescht !=1',
            ];

            $options = array_merge($default, $options);
            foreach ($options as $table => $where) {
                $tableCleaned = $this->cleanerService->tryXssClean($table);
                $whereCleaned = '';
                if (trim($where) !== '') {
                    $whereCleaned = $this->cleanerService->tryXssClean($where);
                }
                $this->customDemoExporter[$tableCleaned] = $whereCleaned;
            }
        } catch (DemoExporterCleanerException $exception) {
            throw new DemoExporterException($exception->getMessage());
        }

        $value = $this->valueToDB($this->customDemoExporter);

        $this->configurationService->trySetConfiguration(static::DEMO_EXPORTER_CONFIG_NAME, $value);
    }

    /**
     * @param array $value
     *
     * @return string
     */
    private function valueToDB($value)
    {
        return base64_encode(serialize($value));
    }

    /**
     * @param stdClass $config
     *
     * @return void
     */
    public function export(stdClass $config)
    {
        $this->logger->write('--Begin--');

        $customDemoExporter = $this->valueFromDB($config->demo_exporter_config);
        $this->zipFile = $config->options->zip_file;
        $tmpDirectory = $this->tmpDir . uniqid('', true) . DIRECTORY_SEPARATOR;
        if (!@mkdir($tmpDirectory, 0777, true) && !is_dir($tmpDirectory)) {
            throw new DemoExporterException(sprintf('Failed to create tmp Dir %s', $tmpDirectory));
        }
        $this->db->perform("SET SESSION SQL_MODE='ALLOW_INVALID_DATES'");

        $this->logger->write('Export DB data');

        try {
            foreach ($customDemoExporter as $table => $where) {
                $tableTmpName = $table . '_' . time();
                $sqlTableAndWhere = empty($where) ? $table : $table . ' WHERE  ' . stripslashes($where);
                $sqlFrom = 'SELECT * FROM ' . $sqlTableAndWhere;
                if ($table === 'artikel') {
                    $articleWhere = !empty($where) ? 'WHERE ' . stripslashes($where) . ' AND ' : 'WHERE ';

                    $articleWhere .= '(v.gueltig_bis ="0000-00-00" OR v.gueltig_bis >NOW()) AND v.adresse IN(0,NULL) 
                                AND v.gruppe IN(0,NULL) AND artikel.geloescht !=1';
                    $sqlFrom = 'SELECT artikel.* FROM artikel AS `artikel` INNER JOIN verkaufspreise AS `v` 
                            ON(artikel.id=v.artikel) 
                            ' . $articleWhere . '
                       ORDER BY v.ab_menge';
                    $tmpSQL = 'CREATE TEMPORARY TABLE IF NOT EXISTS ' . $tableTmpName . ' ' . $sqlFrom;
                    $this->db->perform($tmpSQL);
                    $this->sqlTmpArticle = $tableTmpName;
                }
                $this->sqlToCSV($sqlFrom, $tmpDirectory, $table);
            }
        } catch (QueryFailureException $exception) {
            $this->logger->write('ERROR');
            throw new DemoExporterException($exception->getMessage());
        } catch (ExporterExceptionInterface $exception) {
            $this->logger->write('ERROR');
            throw new DemoExporterException($exception->getMessage());
        }

        $this->logger->write('Grab Data and files');

        if (!empty($this->sqlTmpArticle)) {
            $this->grabArticleData($this->sqlTmpArticle, $tmpDirectory);
        }

        if ($this->zipExport($tmpDirectory)) {
            $this->logger->write('Create achieve');
            $this->deleteDir($tmpDirectory);
        }
        $this->logger->write('--END--');
    }

    /**
     * @param string $value
     *
     * @return mixed
     */
    private function valueFromDB($value)
    {
        return unserialize(base64_decode($value));
    }

    protected function sqlToCSV($sqlFrom, $tmpDirectory, $tableName)
    {
        $tableName = $tmpDirectory . $tableName . '.csv';

        $data = $this->db->yieldAll($sqlFrom);
        $exporter = new CsvExporter();
        $exporter->export($tableName, $data);
    }

    /**
     * @param $tableTmpName
     * @param $tmpDirectory
     */
    private function grabArticleData($tableTmpName, $tmpDirectory)
    {
        if (empty($tableTmpName)) {
            throw new DemoExporterException('Table for grabbing is missing');
        }
        $sql = 'SELECT a.id, dv.dateiname, dv.version, a.nummer FROM ' . $tableTmpName . ' AS `a` 
        LEFT JOIN datei_stichwoerter AS `ds` ON (a.id=ds.parameter)
        INNER JOIN datei AS `d` ON (ds.datei=d.id)
        LEFT JOIN datei_version AS `dv` ON (d.id=dv.datei)
        WHERE LOWER(ds.objekt) =:object AND d.geloescht !=:deleted AND a.geloescht !=:deleted';

        // SQL verkaufpreise
        $salePrices = 'SELECT v.* FROM ' . $tableTmpName . ' AS `a` INNER JOIN verkaufspreise AS `v` ON(a.id=v.artikel) 
                       WHERE (v.gueltig_bis ="0000-00-00" OR v.gueltig_bis >NOW()) AND v.adresse IN(0,NULL) 
                       AND v.gruppe IN(0,NULL) ORDER BY v.ab_menge';

        $this->sqlToCSV($salePrices, $tmpDirectory, 'verkaufspreise');

        try {
            $rows = $this->db->fetchAll($sql, ['object' => 'artikel', 'deleted' => 1]);

        } catch (DatabaseExceptionInterface $exception) {
            throw new DemoExporterException($exception->getMessage());
        }

        $filesDir = $tmpDirectory . 'files' . DIRECTORY_SEPARATOR;
        if (!@mkdir($filesDir, 0777, true) && !is_dir($filesDir)) {
            throw new DemoExporterException(sprintf('Failed to create tmp Dir %s', $filesDir));
        }

        foreach ($rows as $row) {
            $id = $row['id'];
            $articleNumber = $row['nummer'];
            $fileName = $row['dateiname'];
            $pathFile = $this->dateiService->tryGetDateiPfad($id);
            $finalName = $filesDir . $articleNumber . '_' . $fileName;

            if (file_exists($pathFile) && !copy($pathFile, $finalName)) {
                $this->logger->write('ERROR');
                throw new DemoExporterException(sprintf('Failed to copy  %s into tmp Dir %s', $pathFile, $finalName));
            }
        }
        $this->db->perform('DROP TABLE IF EXISTS ' . $tableTmpName);
    }

    /**
     * @param string $tmpDirectory
     *
     * @throws DemoExporterException
     *
     * @return bool
     */
    private function zipExport($tmpDirectory)
    {
        $rootPath = realpath($tmpDirectory);

        $oZip = new ZipArchive();

        $zipFile = $this->tmpDir . $this->zipFile;
        if ($this->openZipObject($oZip, $zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->logger->write('ERROR');
            throw new DemoExporterException(sprintf('Failure to create temporary file in "%s"', $this->tmpDir));
        }

        try {

            /** @var RecursiveIteratorIterator $oFiles */
            $oFiles = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($rootPath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($oFiles as $name => $oFile) {
                /** @var SplFileInfo $oFile */
                if (!$oFile->isDir()) {
                    $filePath = $oFile->getRealPath();
                    $relativePath = substr($filePath, strlen($rootPath) + 1);
                    $oZip->addFile($filePath, $relativePath);
                }
            }

            return $oZip->close();
        } catch (Throwable $exception) {
            $this->logger->write('ERROR');
            throw new DemoExporterException($exception->getMessage());
        }
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
        $this->logger->write('ERROR');
        throw new DemoExporterException(sprintf('Deleted DIR %s failed', $dirPath));
    }

    /**
     * @param string $zipFileName
     *
     * @throws DemoExporterException
     *
     * @return string
     */
    public function getZippedFile($zipFileName)
    {
        $zippedFile = $this->tmpDir . $zipFileName;
        if (!file_exists($zippedFile)) {
            $this->logger->write('ERROR');
            throw new DemoExporterException(sprintf('Zipped file %s cannot be found!', $zipFileName));
        }

        return $zippedFile;
    }

    /**
     * @param array  $xConfig
     * @param string $identifier
     * @param string $sParam
     * @param string $cronFile
     *
     * @throws Exception
     */
    public function addToProcessStarter(
        $xConfig,
        $identifier = 'Demo Exporter Konfigurartion',
        $sParam = 'demo_exporter_cron',
        $cronFile = 'demo_exporter'
    ) {
        try {
            $rowDemoExporter = $this->gateway->getDemoExporterConfigurationValue(static::DEMO_EXPORTER_CONFIG_NAME);
            if (empty($rowDemoExporter)) {
                $this->logger->write('ERROR');
                throw new DemoExporterException(
                    sprintf('configuration value %s cannot be found !', static::DEMO_EXPORTER_CONFIG_NAME)
                );
            }
            $xConfig['demo_exporter_config'] = $rowDemoExporter['wert'];
            $value = json_encode($xConfig);

            $this->backupService->addToProcessStarter($value, $identifier, $sParam, $cronFile);
        } catch (BackupModuleRuntimeException $exception) {
            $this->logger->write('ERROR');
            throw new DemoExporterException($exception->getMessage());
        }
    }
}
