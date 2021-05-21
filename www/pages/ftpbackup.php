<?php
/*
**** COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
* 
* Xentral (c) Xentral ERP Sorftware GmbH, Fuggerstrasse 11, D-86150 Augsburg, * Germany 2019
*
* This file is licensed under the Embedded Projects General Public License *Version 3.1. 
*
* You should have received a copy of this license from your vendor and/or *along with this file; If not, please visit www.wawision.de/Lizenzhinweis 
* to obtain the text of the corresponding license version.  
*
**** END OF COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
*/
?>
<?php

use Xentral\Components\Filesystem\Adapter\FtpConfig;
use Xentral\Components\Filesystem\FilesystemFactory;
use Xentral\Components\Filesystem\PathInfo;

class Ftpbackup
{
  /** @var string MODULE_NAME */
  const MODULE_NAME = 'FtpBackup';

  /** @var Application $app */
  public $app;


  /**
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app = $app;
    if($intern){
      return;
    }

    $this->app->ActionHandlerInit($this);
    $this->app->ActionHandler('list', 'FtpbackupList');
    $this->app->DefaultActionHandler('list');
    $this->app->ActionHandlerListen($app);
  }

  /**
   * @return void
   */
  public function Install()
  {
    $this->app->erp->CheckProzessstarter('FTP-Backup', 'uhrzeit', '', '2019-01-01 00:00:00', 'cronjob', 'ftpbackup', 0);
  }

  /**
   * @return void
   */
  public function FtpbackupList()
  {
    $credentials = $this->GetFtpConfigurationValues();

    if (!empty($credentials['host']) && !empty($credentials['user']) && !empty($credentials['pass'])) {
      try {
        $backupTable = $this->GenerateBackupOverviewTable();
      } catch (Exception $exception) {
        $backupTable = '<tr><td colspan="7">Fehler beim Zugriff auf FTP-Server: ';
        $backupTable .= htmlspecialchars($exception->getMessage());
        $backupTable .= '</td></tr>';
      }
    } else {
      $backupTable = '<tr><td colspan="7">Es sind keine FTP-Zugangsdaten hinterlegt.</td></tr>';
    }

    $this->app->erp->MenuEintrag('index.php?module=ftpbackup&action=list', '&Uuml;bersicht');

    if(!empty($this->app->Secure->GetPOST('save-settings'))){

      $credentials['user'] = $this->app->Secure->GetPOST('benutzer');
      $credentials['host'] = $this->app->Secure->GetPOST('server');
      $credentials['port'] = $this->app->Secure->GetPOST('port');
      $credentials['pass'] = $this->app->Secure->GetPOST('passwort');
      $credentials['dir'] = $this->app->Secure->GetPOST('verzeichnis');

      $this->app->erp->SetKonfigurationValue('ftpbackup_list_port',$credentials['port']);
      $this->app->erp->SetKonfigurationValue('ftpbackup_list_server',$credentials['host']);
      $this->app->erp->SetKonfigurationValue('ftpbackup_list_benutzer',$credentials['user']);
      $this->app->erp->SetKonfigurationValue('ftpbackup_list_passwort',$credentials['pass']);
      $this->app->erp->SetKonfigurationValue('ftpbackup_list_verzeichnis',$credentials['dir']);
    }

    $this->app->erp->checkActiveCronjob('ftpbackup');

    $this->app->Tpl->Set('BACKUPROW', $backupTable);
    $this->app->Tpl->Set('BENUTZER', $credentials['user']);
    $this->app->Tpl->Set('SERVER', $credentials['host']);
    $this->app->Tpl->Set('PORT', $credentials['port']);
    $this->app->Tpl->Set('PASSWORT', $credentials['pass']);
    $this->app->Tpl->Set('VERZEICHNIS', $credentials['dir']);
    $this->app->Tpl->Parse('PAGE', 'ftpbackup_list.tpl');
  }

  /**
   * @return string HTML
   */
  protected function GenerateBackupOverviewTable()
  {
    $cred = $this->GetFtpConfigurationValues();

    /** @var FilesystemFactory $fsFactory */
    $fsFactory = $this->app->Container->get('FilesystemFactory');
    $ftpConfig = new FtpConfig($cred['host'], $cred['user'], $cred['pass'], $cred['dir'], $cred['port']);
    $ftp = $fsFactory->createFtp($ftpConfig);

    $sqldumpMap = [];
    $userdataMap = [];
    for ($day = 1; $day <= 31; $day++) {
      $sqldumpMap[$day] = null;
      $userdataMap[$day] = null;
    }

    // FTP durchsuchen und Dateien in entsprechende Map (Tag als Index) packen
    $files = $ftp->listFiles('', false);
    foreach ($files as $index => $file) {
      $fileName = $file->getFilename();
      $day = $this->ExtractDayFromFilename($fileName);
      if (substr($fileName, 0, 5) === 'mysql' && $day !== null) {
        $sqldumpMap[$day] = $file;
      }
      if (substr($fileName, 0, 8) === 'userdata' && $day !== null) {
        $userdataMap[$day] = $file;
      }
    }

    // HTML-Tabelle mit gefundenen Dateien zusammenbauen
    $html = '';
    for ($day = 1; $day <= 31; $day++) {
      $row = [
        'day' => '',
        'sqldump_name' => '',
        'sqldump_date' => '',
        'sqldump_size' => '',
        'userdata_name' => '',
        'userdata_date' => '',
        'userdata_size' => '',
      ];

      if ($sqldumpMap[$day] !== null) {
        /** @var PathInfo $sqlDump */
        $sqlDump = $sqldumpMap[$day];
        $row['sqldump_name'] = $sqlDump->getBasename();
        $row['sqldump_date'] = date('d.m.Y', $sqlDump->getTimestamp());
        $row['sqldump_size'] = $this->FormatBytes($sqlDump->getSize());
        unset($sqlDump);
      }

      if ($userdataMap[$day] !== null) {
        /** @var PathInfo $userdata */
        $userdata = $userdataMap[$day];
        $row['userdata_name'] = $userdata->getBasename();
        $row['userdata_date'] = date('d.m.Y', $userdata->getTimestamp());
        $row['userdata_size'] = $this->FormatBytes($userdata->getSize());
        unset($userdata);
      }

      $html .= sprintf(
        '<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
        $day,
        $row['sqldump_date'],
        $row['sqldump_name'],
        $row['sqldump_size'],
        $row['userdata_date'],
        $row['userdata_name'],
        $row['userdata_size']
      );
    }

    return $html;
  }

  /**
   * @return array
   */
  protected function GetFtpConfigurationValues()
  {
    $credentials = [
      'host' => $this->app->erp->GetKonfiguration('ftpbackup_list_server'),
      'port' => (int)$this->app->erp->GetKonfiguration('ftpbackup_list_port'),
      'user' => $this->app->erp->GetKonfiguration('ftpbackup_list_benutzer'),
      'pass' => $this->app->erp->GetKonfiguration('ftpbackup_list_passwort'),
      'dir' => $this->app->erp->GetKonfiguration('ftpbackup_list_verzeichnis'),
    ];

    if(empty($credentials['dir'])){
      $credentials['dir'] = '/';
    }
    if(empty($credentials['port'])){
      $credentials['port'] = 21;
    }

    return $credentials;
  }

  /**
   * @param string $filename
   *
   * @return int|null
   */
  protected function ExtractDayFromFilename($filename)
  {
    preg_match('/\d{1,2}/', $filename, $matches);

    return !empty($matches[0]) ? (int)$matches[0] : null;
  }

  /**
   * @param int $bytes
   *
   * @return string
   */
  protected function FormatBytes($bytes)
  {
    $bytes = (int)$bytes;
    if ($bytes <= 0) {
      return '0&nbsp;Bytes';
    }

    $units = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    $exponent = (int)floor(log($bytes) / log(1024));
    $size = $bytes / pow(1024, $exponent);

    return number_format($size, 1, ',', '.') . '&nbsp;' . $units[$exponent];
  }
}

?>
