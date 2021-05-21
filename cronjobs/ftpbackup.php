<?php

/**
 * Automatische FTP-Backups
 *
 * Prozessstarter erzeugt jeden Tag ein Backup von
 * - userdata-Verzeichnis (gezippt)
 * - SQL-Dump der Datenbank (gezippt)
 * und lädt diese Backups auf den hinterlegten FTP-Server hoch.
 *
 * FTP-Zugangsdaten pflegen über Ftpbackup-Modul
 * (index.php?module=ftpbackup&action=list)
 */

use Xentral\Components\Filesystem\Adapter\FtpConfig;
use Xentral\Components\Filesystem\FilesystemFactory;
use Xentral\Components\Filesystem\FilesystemInterface;

try {

  $ftpHost = $app->erp->GetKonfiguration('ftpbackup_list_server');
  $ftpPort = (int)$app->erp->GetKonfiguration('ftpbackup_list_port');
  $ftpUser = $app->erp->GetKonfiguration('ftpbackup_list_benutzer');
  $ftpPass = $app->erp->GetKonfiguration('ftpbackup_list_passwort');
  $ftpDir = $app->erp->GetKonfiguration('ftpbackup_list_verzeichnis');

  if(empty($ftpHost)){
    throw new RuntimeException('FTP hostname is missing or empty.');
  }
  if(empty($ftpUser)){
    throw new RuntimeException('FTP username is missing or empty.');
  }
  if(empty($ftpPass)){
    throw new RuntimeException('FTP password is missing or empty.');
  }
  if(empty($ftpDir)){
    $ftpDir = '/';
  }
  if(empty($ftpPort) || $ftpPort <= 0){
    $ftpPort = 21;
  }

  /** @var FilesystemFactory $factory */
  $factory = $app->Container->get('FilesystemFactory');
  $ftpConfig = new FtpConfig($ftpHost, $ftpUser, $ftpPass, $ftpDir, $ftpPort);
  $ftpFs = $factory->createFtp($ftpConfig);

  $ftpBackup = new FtpBackupCronjob($app->Conf, $ftpFs);
  $ftpBackup->execute();
  $ftpBackup->cleanup();

} catch (Exception $exception) {
  if (isset($ftpBackup) && $ftpBackup !== null) {
    $ftpBackup->cleanup();
  }
  throw $exception;
}


final class FtpBackupCronjob
{
  /** @var Config $config */
  private $config;

  /** @var FilesystemInterface $ftp */
  private $ftp;

  /** @var string $tmpUserdataFilePath */
  private $tmpUserdataFilePath;

  /** @var string $tmpMysqlDumpFilePath */
  private $tmpMysqlDumpFilePath;

  /**
   * @param Config              $config
   * @param FilesystemInterface $ftp
   */
  public function __construct(Config $config, FilesystemInterface $ftp)
  {
    $this->ftp = $ftp;
    $this->config = $config;
    $this->tmpUserdataFilePath = tempnam(sys_get_temp_dir(), 'xentral_userdata_backup');
    $this->tmpMysqlDumpFilePath = tempnam(sys_get_temp_dir(), 'xentral_mysql_backup');

    if(!is_file($this->tmpMysqlDumpFilePath)){
      throw new RuntimeException(sprintf('Could not create temp file: %s', $this->tmpMysqlDumpFilePath));
    }
    if(!is_file($this->tmpUserdataFilePath)){
      throw new RuntimeException(sprintf('Could not create temp file: %s', $this->tmpUserdataFilePath));
    }
  }

  /**
   */
  public function __destruct()
  {
    $this->cleanup();
  }

  /**
   * @return void
   */
  public function cleanup()
  {
    $this->deleteFile($this->tmpUserdataFilePath);
    $this->deleteFile($this->tmpMysqlDumpFilePath);
  }

  /**
   * @return void
   */
  public function execute()
  {
    $this->createMysqlBackup($this->tmpMysqlDumpFilePath);
    $this->createUserdataBackup($this->tmpUserdataFilePath);

    // d =Day of the month, 2 digits with leading zeros; Excaple: 01 to 31
    $this->copyFileToFtp($this->tmpUserdataFilePath, 'userdata_' . date('d') . '.tar.gz');
    $this->copyFileToFtp($this->tmpMysqlDumpFilePath, 'mysqldump_' . date('d') . '.gz');
  }

  /**
   * @param string $dumpFilePath Absolute path to mysqldump
   *
   * @return void
   * @throws RuntimeException
   *
   */
  private function createMysqlBackup($dumpFilePath)
  {
    $dbHost = property_exists($this->config, 'WFdbhost') ? $this->config->WFdbhost : 'localhost';
    $dbPort = property_exists($this->config, 'WFdbport') ? (int)$this->config->WFdbport : 3306;
    $dbName = property_exists($this->config, 'WFdbname') ? $this->config->WFdbname : null;
    $dbUser = property_exists($this->config, 'WFdbuser') ? $this->config->WFdbuser : null;
    $dbPass = property_exists($this->config, 'WFdbpass') ? $this->config->WFdbpass : null;

    if(empty($dbName)){
      throw new RuntimeException('Database name is missing or empty.');
    }
    if(empty($dbUser)){
      throw new RuntimeException('Database user is missing or empty.');
    }
    if(empty($dbPass)){
      throw new RuntimeException('Database password is missing or empty.');
    }

    $dumpCmd = sprintf(
      'mysqldump --no-tablespaces --host=%s --port=%s --user=%s --password=%s --databases %s',
      escapeshellarg($dbHost),
      $dbPort,
      escapeshellarg($dbUser),
      escapeshellarg($dbPass),
      escapeshellarg($dbName)
    );
    $zipCmd = sprintf('gzip > %s', escapeshellarg($dumpFilePath));
    $command = $dumpCmd . ' | ' . $zipCmd;

    @exec($command, $output, $returnVar);
    $output = implode("\n", $output);

    if($returnVar !== 0){
      throw new RuntimeException('Mysql dump command failed. ' . $output);
    }
  }

  /**
   * @param string $backupFilePath Absolute path to archive file
   *
   * @return void
   * @throws RuntimeException
   *
   */
  private function createUserdataBackup($backupFilePath)
  {
    $userdataDir = property_exists($this->config, 'WFuserdata')
      ? $this->config->WFuserdata
      : dirname(__DIR__) . '/userdata';

    // 2>&1 = Fehlerausgabe-Kanal in Standardausgabe-Kanal schreiben
    $command = sprintf(
      'tar cfz %s %s 2>&1',
      escapeshellarg($backupFilePath),
      escapeshellarg($userdataDir)
    );

    @exec($command, $output, $returnVar);
    $output = implode("\n", $output);

    if($returnVar !== 0){
      throw new RuntimeException('Userdata backup command failed. ' . $output);
    }
  }

  /**
   * @param string $sourceFilePath Absolute path
   * @param string $targetFilename File name; without dir
   *
   * @return void
   * @throws RuntimeException If operation fails
   *
   */
  private function copyFileToFtp($sourceFilePath, $targetFilename)
  {
    if(!is_file($sourceFilePath)){
      throw new RuntimeException(sprintf('Source file not found: %s', $sourceFilePath));
    }
    $resource = @fopen($sourceFilePath, 'rb');
    if(!is_resource($resource)){
      throw new RuntimeException(sprintf('Source file not readable: %s', $sourceFilePath));
    }

    // Datei anlegen oder überschreiben
    $success = $this->ftp->putStream($targetFilename, $resource);
    if(!$success){
      throw new RuntimeException(sprintf('Could not write file to FTP: %s', $targetFilename));
    }
  }

  /**
   * @param string $filePath
   *
   * @return void
   */
  private function deleteFile($filePath)
  {
    if(is_file($filePath)){
      @unlink($filePath);
    }
  }
}
