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

use Xentral\Components\Backup\Exception\LogException;
use Xentral\Components\Backup\Logger\BackupLog;
use Xentral\Components\Http\Request;
use Xentral\Modules\Backup\BackupGateway;
use Xentral\Modules\Backup\BackupService;
//use Xentral\Modules\Backup\BackupSystemConfigurationService;
use Xentral\Widgets\ChunkedUpload\ChunkedUploadRequestHandler;

class Backup
{
  /** @var string MODULE_NAME */
  const MODULE_NAME = 'Backup';

  /** @var Application $app */
  private $app;

  /** @var BackupService $service */
  private $oBackupService;

  /** @var BackupGateway $gateway */
  private $oBackupGateway;

  /**
   * Backup constructor.
   *
   * @param      $app
   * @param bool $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app = $app;
    try {
      $this->oBackupService = $this->app->Container->get('BackupService');
      $this->oBackupGateway = $this->app->Container->get('BackupGateway');
    } catch (RuntimeException $e) {
      $this->app->Tpl->Set('MESSAGE', '<div class="error">Backup Fehler: ' . $e->getMessage() . '</div>');
    }

    if($intern){
      return;
    }

    $id = $this->app->Secure->GetGET('id');
    if(is_numeric($id)){
      $this->app->Tpl->Set('SUBHEADING', ": " . $this->app->DB->Select("SELECT nummer FROM artikel WHERE id=$id LIMIT 1"));
    }

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler('list', 'BackupList');
    $this->app->ActionHandler('create', 'BackupCreate');
    $this->app->ActionHandler('recover', 'BackupRecover');
    $this->app->ActionHandler('readstatus', 'ReadStatus');
    $this->app->ActionHandler("downloadsnapshot", "BackupDownloadSnapshot");
    $this->app->ActionHandler("delete", "BackupDelete");
    $this->app->ActionHandler("importer", "BackupImporter");


    $this->host = $this->app->Conf->WFdbhost;
    $this->database = $this->app->Conf->WFdbname;
    $this->user = $this->app->Conf->WFdbuser;
    $this->password = $this->app->Conf->WFdbpass;

    $this->pfad = (isset($this->app->Conf->WFbackup) && is_dir($this->app->Conf->WFbackup) ? rtrim($this->app->Conf->WFbackup, '/') . "/" : "../backup/snapshots/");

    $this->app->ActionHandlerListen($app);

    $this->app->erp->Headlines('Backup');
  }

  public function Install()
  {
    // ADD Cron
    $check = $this->app->DB->SelectRow("SELECT `id` FROM `prozessstarter` WHERE `parameter` = 'backup' LIMIT 1");
    $dateTime = new DateTime('tomorrow');
    $startTime = sprintf('%s %s', $dateTime->format('Y-m-d'), '02:00:00');
    if(empty($check)){
      $this->app->erp->CheckProzessstarter('Backup', 'periodisch', 1440, $startTime, 'cronjob', 'backup', 1);
    }else{
      // Backword compatibility
      $this->app->DB->Update(
        sprintf(
          "UPDATE `prozessstarter` SET `bezeichnung` = '%s', `periode`=%d, `recommended_period`=%d, `startzeit`='%s' WHERE `id` = %d",
          'Backup', 1440, 1440, $startTime, $check['id']
        )
      );
    }
  }

  /**
   * @param $app
   * @param $name
   * @param $erlaubtevars
   *
   * @return array
   */
  /*public function TableSearch(&$app, $name, $erlaubtevars)
  {
    switch ($name) {
      case 'backuplist':
        $allowed['backup'] = array('list');
        $heading = array('Name', 'Dateiname', 'Datum', 'Men&uuml;');
        $width = array('30%', '30%', '20%', '8%');
        $findcols = array('name', 'dateiname', 'datum', 'id');
        $searchsql = array('name', "DATE_FORMAT(datum, '%d.%m.%Y %H:%i:%s')");
        $sql = "SELECT SQL_CALC_FOUND_ROWS id, name, dateiname, DATE_FORMAT(datum, '%d.%m.%Y %H:%i:%s'), id as menu FROM backup";
        $defaultorder = 4; //Optional wenn andere Reihenfolge gewuenscht
        $defaultorderdesc = 1;

        $where = "";
        $count = "SELECT COUNT(id) FROM backup";
        $menu = "<a href=\"#\" id=\"recover-backup\" data-id=\"%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/backward.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"index.php?module=backup&action=downloadsnapshot&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/download.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=backup&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";
        break;
    }

    $erg = [];
    foreach ($erlaubtevars as $k => $v) {
      if(isset($$v)){
        $erg[$v] = $$v;
      }
    }
    return $erg;
  }*/

  /**
   * @void
   */
  public function BackupImporter()
  {
    $this->BackupMenu();
    if(!empty($cmd = $this->app->Secure->GetGET('cmd')) && $cmd === 'upload'){
      /** @var Request $request */
      $request = $this->app->Container->get('Request');

      /** @var ChunkedUploadRequestHandler $handler */
      $handler = $this->app->Container->get('ChunkedUploadRequestHandler');

      $tempDir = sys_get_temp_dir();

      $saveDir = $this->app->erp->GetRootPath() . '/backup/snapshots/';
      if(!file_exists($saveDir) && !mkdir($saveDir, 0777, true) && !is_dir($saveDir)){
        throw new RuntimeException(sprintf('Directory "%s" was not created', $saveDir));
      }
      $response = $handler->handleRequest($request, $tempDir, $saveDir);
      $response->send();
      $this->app->erp->ExitWawi();
    }
    if(!empty($cmd = $this->app->Secure->GetGET('cmd')) && $cmd === 'completed'){

      if($sFileName = $this->app->Secure->GetPOST('file_name')){

        $fileNameExploded = explode('.', $sFileName);
        array_pop($fileNameExploded);
        $backupName = implode('.', $fileNameExploded);
        $address = $this->app->User->GetAdresse();

        $this->app->DB->Insert("INSERT INTO backup (adresse, name, dateiname, datum) 
                                VALUES ('$address','$backupName','$sFileName',NOW())"
        );
      }

      $xResponse = [
        'completed' => true,
        'message' => $this->app->erp->base64_url_encode('<div class="error">Backup Import erfolreich abgeschlossen.</div>')
      ];

      $this->ViewJsonEncode($xResponse);
    }

    $this->app->ModuleScriptCache->IncludeWidgetNew('ChunkedUpload');

    $this->app->Tpl->Parse('PAGE', "backup_upload.tpl");
  }

  /**
   * @return void
   */
  public function BackupMenu()
  {
    $this->app->erp->MenuEintrag("index.php?module=backup&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=backup&action=list", "&Uuml;bersicht");
    //$this->app->erp->MenuEintrag("index.php?module=backup&action=importer", "Importer");
    //$this->app->erp->MenuEintrag("BackupModule.createItem()", "Neuer Eintrag");
  }

  /**
   * @param mixed $xValue
   *
   * @return void
   */
  public function ViewJsonEncode($xValue)
  {
    header('Content-type: application/json');
    echo json_encode($xValue);
    if(!empty($this->app->erp)){
      $this->app->erp->ExitWawi();
    }
  }

  /**
   * List all existing Backups
   *
   * @return void
   * @throws Exception
   */
  public function BackupList()
  {
    $this->BackupMenu();
    $this->app->Tpl->Set('UEBERSCHRIFT', "Backup");
    $this->app->Tpl->Set('KURZUEBERSCHRIFT', "Backup");
    //$this->app->YUI->TableSearch('TAB1', 'backuplist', 'show', '', '', basename(__FILE__), __CLASS__);
    $this->app->Tpl->Set('TABTEXT', "Backup");

    $this->app->erp->checkActiveCronjob('backup');
    /*
    $processStarterEnabled = 1;

    $backupConfService = $this->app->Container->get('BackupSystemConfigurationService');
    if($backupConfService->tryCheckCronIsEnabled() === false){
      $processStarterEnabled = 0;
      $this->app->Tpl->Set('MESSAGE', '<div class="error">Es sieht so aus, als ob der Prozessstarter Backup nicht regelm&auml;&szlig;ig ausgef&uuml;hrt wird! Bitte aktivieren Sie diesen (<a href="index.php?module=prozessstarter&action=list" target="_blank">zu den Prozessstartern</a>)!</div>');
    }
   // $this->app->Tpl->Set('PROCESS_STARTER_STATUS', $processStarterEnabled);

    $free = disk_free_space($this->app->erp->GetRootPath());

    $free /= 1024 * 1024;
    $minFree = (int)$this->app->DB->Select(
        sprintf(
          "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024)  
        FROM information_schema.TABLES
        WHERE table_schema = '%s'",
          $this->app->Conf->WFdbname
        )
      ) + 512;

    $userdata = (int)$this->app->erp->GetKonfiguration('userdatasize');

    if($free > 0 && $minFree + $userdata > $free){
      $this->app->Tpl->Add(
        'MESSAGE',
        sprintf(
          '<div class="error">Es ist nur %d MB Speicher auf dem System frei, es werden aber mindestens %d MB f&uuml;r ein 
          Datenbank-Backup ben&ouml;tigt.</div>',
          $free, $minFree + $userdata
        )
      );
    }*/

    $sqlProcessStarter = "SELECT ps.letzteausfuerhung, ps.periode FROM `prozessstarter` AS `ps` WHERE ps.parameter = 'backup' LIMIT 1";
    $pStarter = $this->app->DB->SelectRow($sqlProcessStarter);
    // LATEST BACKUP
    if($lastestBackup = $this->oBackupGateway->getLatestBackup()){
      //$nextRun = (new DateTime($pStarter['letzteausfuerhung']))->getTimestamp() + (int)$pStarter['periode'] * 60;

      $latestBackupTime = new DateTime($lastestBackup['datum']);
      $latestMsg = sprintf('Letztes Backup %s Uhr.', $latestBackupTime->format('d.m.Y H:i'));
      $this->app->Tpl->Add('MESSAGE_DOWNLOAD', '<div class="success backup-success">' . $latestMsg . '
      <a class="button button-neutral remove-backup" data-url="index.php?module=backup&action=delete&id='.$lastestBackup['id'].'" role="button" href="#">Löschen</a>
      <a href="index.php?module=backup&action=downloadsnapshot&id=' . $lastestBackup['id'] . '" class="button">Herunterladen</a>
      </div>');
    }

    $dateTime = new DateTime('yesterday');
    $startTime = $dateTime->format('Y-m-d');
    $today = (new DateTime())->format('Y-m-d');
    $backupFail = null;
    $startIdSQL = "SELECT cl.id FROM cronjob_log AS `cl` WHERE cl.cronjob_name = 'backup' AND cl.status = 'start'
                                        AND (DATE(cl.change_time)='%s' OR DATE(cl.change_time)='%s' )";

    $allStartIds = $this->app->DB->SelectFirstCols(sprintf($startIdSQL, $startTime, $today));
    if(!empty($allStartIds)){
      $failSQL = "SELECT cl.id FROM cronjob_log AS `cl` WHERE cl.status='error' AND cl.parent_id IN (%s) ";
      $backupFail = $this->app->DB->SelectArr(sprintf($failSQL, implode(',', $allStartIds)));
    }
    if($backupFail !== null){
      $lastestLogError = '';
      $logErrorMsg = '';
      $dateError = '';
      try {
        $lastestLogError = $this->GetLogger()->tail(0, null, $this->GetLogger()->getPersistentFileName());
      } catch (LogException $exception) {
        // do nothing
      }
      if (!empty($lastestLogError)){
        $lastestLogErrorExploded = explode(':', $lastestLogError);
        $dateError = array_shift($lastestLogErrorExploded);

        $dateError = sprintf('am %s', date('d.m.Y H:i', $dateError));
        $logErrorMsg = sprintf('Grund: <strong>%s</strong>', implode('', $lastestLogErrorExploded));
      }

      $msgError = sprintf('Das letzte Backup konnte %s nicht erstellt werden. %s', $dateError, $logErrorMsg);
      $msg = sprintf('<div class="error">%s</div>', $msgError);
      $this->app->Tpl->Add('MESSAGE_ERROR', $msg);
    }
    // CHECK ZIP-TOOL
    if(!$this->oBackupService->hasExecutableExtension('zip')){
      if (extension_loaded('zip')) {
        $msg = sprintf('<div class="error">%s</div>', 'Es fehlt das Kommandozeilen-Tool "zip" auf dem Server. Bitte installieren Sie, dass es auf der Kommandozeile verfügbar ist. Es ist nicht die Erweiterung php-zip gemeint, diese ist korrekt vorhanden');
      } else{
        $msg = sprintf('<div class="error">%s</div>', 'Es fehlt das Kommandozeilen-Tool "zip" auf dem Server. Bitte installieren Sie, dass es auf der Kommandozeile verfügbar ist');
      }
      $this->app->Tpl->Add('MESSAGE_ERROR', $msg);
    }
    if(!empty($pStarter) && $this->app->erp->isSystemBlockedByBackup()){
      $lastRun = new DateTime($pStarter['letzteausfuerhung']);
      $lastRunMsg = sprintf('Aktuell wird ein Backup (gestartet am %s um %s) erstellt! Bitte warten bis das Backup bereit steht.', $lastRun->format('d.m.Y'), $lastRun->format('H:i'));
      $msg = sprintf('<div class="warning">%s</div>', $lastRunMsg);
      $this->app->Tpl->Add('MESSAGE_RUNNING', $msg);
    }

    $this->app->Tpl->Parse('PAGE', 'backup.tpl');
  }

  /**
   * Creates a Backup
   *
   * @return void
   * @throws Exception
   */
  public function BackupCreate()
  {
    $name = $this->app->Secure->GetPOST("name");
    $bStatus = false;
    $message = $this->app->erp->base64_url_encode("<div class=\"info\">Sie m&uuml;ssen einen Namen f&uuml;r das Datenbank-Backup eingeben.</div>");
    if(!empty($name)){
      $adresse = $this->app->User->GetAdresse();
      $name = preg_replace('/[^a-zA-Z]+/', '', $name);
      $dateiname = date('Y-m-d_') . $this->database . '_' . $name . '.' . $this->oBackupService->getArchiveExtension();

      if($this->app->DB->Select("SELECT '1' FROM backup WHERE dateiname='$dateiname' LIMIT 1") == '1'){
        $message = $this->app->erp->base64_url_encode("<div class=\"info\">Ein Backup mit diesem Namen existiert bereits.</div>");
      }else{
        $sConfig = json_encode(
          [
            'action' => 'RunCreateJob',
            'config' => $this->app->Conf,
            'file_name' => $dateiname,
            'options' => ['addr' => $adresse, 'name' => $name, 'ssid' => session_id(), 'user_id' => $this->app->User->GetID(), 'ip' => $_SERVER['REMOTE_ADDR']]
          ]);

        $bStatus = $this->oBackupService->addToProcessStarter($sConfig);
        $message = '';
        if($bStatus === false){
          $message = $this->app->erp->base64_url_encode("<div class=\"info\">Das Backup kann momentan nicht erstellt werden. Bitte versuchen Sie es später.</div>");
        }
      }
    }
    $ErrorMsg = $this->app->erp->base64_url_encode(
      "<div class=\"error\">Das Backup konnte nicht erstellt werden.</div>"
    );
    $genericSuccess = 'Backup Erstellung gestartet, Bitte warten bis das Backup bereit steht.';
    $this->ViewJsonEncode([
      'status' => $bStatus,
      'message' => $message,
      'generic_error' => $ErrorMsg,
      'created_at' => time(),
      'backup_file' => $dateiname,
      'success_msg' => $this->app->erp->base64_url_encode("<div class=\"error2\">".$genericSuccess."</div>"),
    ]);
  }

  /**
   * Deletes an existing Backup
   *
   * @return void
   */
  public function BackupDelete()
  {
    $id = $this->app->Secure->GetGET("id");

    $error = false;

    if(is_numeric($id)){
      $dateiname = $this->app->DB->Select("SELECT dateiname FROM backup WHERE id='$id' LIMIT 1");

      if($dateiname != ''){
        $backupFile = $this->pfad . $dateiname;
        if(file_exists($backupFile)){
          unlink($backupFile);
        }
        // REMOVE META FILE AS WELL
        $asFile = explode('.', $backupFile);
        array_pop($asFile);
        $sMetaFile = implode('.', $asFile) . '.meta';
        if(file_exists($sMetaFile)){
          unlink($sMetaFile);
        }
        $this->app->DB->Delete("DELETE FROM backup WHERE id='$id' LIMIT 1");
        $msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Das Backup wurde erfolgreich gel&ouml;scht.</div>");
      }else{
        $error = true;
      }

    }else{
      $error = true;
    }

    if($error){
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Das Backup konnte nicht gel&ouml;scht werden.</div>");
    }

    $this->app->Location->execute("index.php?module=backup&action=list&msg=$msg");
  }

  /**
   * Download a Backup
   *
   * @return void
   */
  public function BackupDownloadSnapshot()
  {
    $id = $this->app->Secure->GetGET("id");
    if(is_numeric($id)){
      $dateiname = $this->app->DB->Select("SELECT dateiname FROM backup WHERE id='$id' LIMIT 1");
      $pfad = $this->pfad . $dateiname;
      if(file_exists($pfad)){
        header("Content-Disposition: attachment; filename=$dateiname");
        header('Content-Length: ' . filesize($pfad));
        $this->readfile_chunked($pfad); //readfile will stream the file.
        $this->app->ExitXentral();
      }
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Die passende Snapshot Datei konnte nicht gefunden werden!</div>");
      header("Location: ./index.php?module=backup&action=list&msg=$msg");
    }
  }

  /**
   * @param      $filename
   * @param bool $retbytes
   *
   * @return bool|int
   */
  protected function readfile_chunked($filename, $retbytes = true)
  {
    $chunksize = 1 * (1024 * 1024); // how many bytes per chunk
    $buffer = '';
    $cnt = 0;
    $handle = fopen($filename, 'rb');
    if($handle === false){
      return false;
    }
    while (!feof($handle)) {
      $buffer = fread($handle, $chunksize);
      echo $buffer;
      if($retbytes){
        $cnt += strlen($buffer);
      }
    }
    $status = fclose($handle);
    if($retbytes && $status){
      return $cnt;
    }
    return $status;

  }

  /**
   * @return BackupLog
   */
  protected function GetLogger()
  {
    return $this->app->Container->get('BackupLog');
  }

  /**
   * Reads running Backup status
   *
   * @return void
   */
  public function ReadStatus()
  {
    $bStatus = false;
    $saveDir = $this->app->erp->GetRootPath() . '/backup/snapshots/';
    try {
      $sMessage = $this->GetLogger()->tail();
    } catch (LogException $exception) {
      // Job net yet started
      $sMessage = ':Please wait';
    }

    $asMessage = explode(':', $sMessage);
    array_shift($asMessage);
    $message = implode(' ', $asMessage);
    if(trim($message) === '--END--'){
      $msg = 'Backup-Vorgang erfolgreich abgeschlossen.';
      $class = 'error2';

      if($sFileName = $this->app->Secure->GetPOST('file_name')){
        $asStatusCheckSum = $this->oBackupService->checkSumOnAfterRecovery($sFileName, $this->app->Conf->WFuserdata);
        if($asStatusCheckSum !== null && !empty($asStatusCheckSum)){
          $class = 'warning';
          $msg = sprintf('Die folgende(n) Tabelle(n) <strong>%s</strong> konnten eventuell nicht komplett wiederhergestellt werden!',
            implode(',', $asStatusCheckSum));
        }
      }
      $bStatus = true;
      $this->oBackupService->removeLoggerFiles();
      $message = $this->app->erp->base64_url_encode('<div class="' . $class . '">' . $msg . '</div>');
    }

    if(trim($message) === 'ERROR'){
      $backupFile = empty($this->app->Secure->GetPOST('backup_file')) ? null : $this->app->Secure->GetPOST('backup_file');
      $this->app->erp->setMaintainance(false);
      $delBackupFile = null !== $backupFile ? $saveDir . $backupFile : null;
      $this->oBackupService->removeLoggerFiles($delBackupFile);
      throw new RuntimeException('ERROR');
    }

    // CHECK IF STILL RUNNING

    if((int)$createdAt = $this->app->Secure->GetPOST('created_at')){
      $backupFile = empty($this->app->Secure->GetPOST('backup_file')) ? null : $this->app->Secure->GetPOST('backup_file');
      $checkResult = $this->checkFalsePositive($backupFile);
      if($checkResult){
        $msg = 'Backup-Vorgang erfolgreich abgeschlossen.';
        $class = 'error2';
        $bStatus = true;
        $this->oBackupService->removeLoggerFiles();
        $message = $this->app->erp->base64_url_encode('<div class="' . $class . '">' . $msg . '</div>');
        $this->ViewJsonEncode([
          'finished' => $bStatus,
          'message' => $message,
        ]);
      }
      $hangCheckStart = 600; // 10min
      if($bStatus === false && (time() - $createdAt > $hangCheckStart) && $this->app->erp->isSystemBlockedByBackup() === false){
        $this->app->erp->setMaintainance(false);
        $delBackupFile = null !== $backupFile ? $saveDir . $backupFile : null;

        $this->oBackupService->removeLoggerFiles($delBackupFile);
        throw new RuntimeException('ERROR: Timeout override');
      }
    }

    $this->ViewJsonEncode([
      'finished' => $bStatus,
      'message' => $message,
    ]);
  }

  private function checkFalsePositive($backupFile = null)
  {
    $saveDir = $this->app->erp->GetRootPath() . '/backup/snapshots/';

    if(null !== $backupFile){
      $dbRecord = $this->app->DB->Select("SELECT '1' FROM backup WHERE dateiname='$backupFile' LIMIT 1") == '1';
      if(file_exists($saveDir . $backupFile) && empty($dbRecord)){
        // fix it add in db
        $address = $this->app->User->GetAdresse();
        $this->app->DB->Insert("INSERT INTO backup (adresse, name, dateiname, datum) VALUES ('$address',$backupFile,$backupFile,NOW())");
        return true;
      }

      if(file_exists($saveDir . $backupFile) && !empty($dbRecord)){
        return true;
      }
    }
    return false;
  }

  /**
   * Runs the restore backup job
   *
   * @param stdClass $oConfig
   *
   * @return void
   */
  public function RunRestoreJob($oConfig)
  {
    if(!empty($oConfig)){
      $this->oBackupService->restore(
        $this->stdClassToConfig($oConfig->config),
        $oConfig->file_name,
        (array)$oConfig->options
      );
    }
  }

  /**
   * Converts stdClass to Config Class
   *
   * @param stdClass $config
   *
   * @return mixed
   */
  private function stdClassToConfig($config)
  {
    if(!($config instanceof stdClass)){
      return $config;
    }
    return unserialize(sprintf(
      'O:%d:"%s"%s',
      strlen('Config'),
      'Config',
      strstr(strstr(serialize($config), '"'), ':')
    ));
  }

  /**
   * Runs the create backup job
   *
   * @param stdClass $oConfig
   *
   * @return void
   */
  public function RunCreateJob($oConfig)
  {
    if(!empty($oConfig)){
      $this->oBackupService->create($this->stdClassToConfig($oConfig->config), $oConfig->file_name, (array)$oConfig->options);
    }
  }

  /**
   * Action recover backup
   *
   * @return void
   * @throws Exception
   */
  public function BackupRecover()
  {
    $id = (int)$this->app->Secure->GetPOST("id");

    if(!empty($id) && ($dateiname = $this->app->DB->Select("SELECT dateiname FROM backup WHERE id='$id' LIMIT 1")) &&
      file_exists($this->oBackupService->getArchivePath($dateiname, $this->app->Conf->WFuserdata))){

      if(!empty($cmd = $this->app->Secure->GetGET('cmd')) && $cmd === 'check-meta'){
        $this->CheckUserInDump();
      }

      $recoverOption = ['ssid' => session_id(), 'user_id' => $this->app->User->GetID(), 'ip' => $_SERVER['REMOTE_ADDR']];

      if($oldDB = $this->app->Secure->GetPOST("old_db")){
        $recoverOption['old_dbname'] = $oldDB;
      }

      $sConfig = json_encode(
        [
          'action' => 'RunRestoreJob',
          'config' => $this->app->Conf,
          'file_name' => $dateiname,
          'options' => $recoverOption,

        ]
      );
      $ErrorMsg = $this->app->erp->base64_url_encode(
        "<div class=\"error\">Das Backup konnte nicht wieder hergestellt werden.</div>"
      );
      $bStatus = $this->oBackupService->addToProcessStarter($sConfig);
      $message = $bStatus === true ? '' :
        $this->app->erp->base64_url_encode('<div class="error2">Das Backup kann momentan nicht gestartet werden. Bitte Versuchen Sie später.</div>');
      $xResponse = [
        'status' => $bStatus,
        'message' => $message,
        'file_name' => $dateiname,
        'created_at' => time(),
        'generic_error' => $ErrorMsg
      ];
    }else{
      $xResponse = [
        'status' => false,
        'missing_file' => true,
        'message' => $this->app->erp->base64_url_encode('<div class="error">Backup konnte nicht gefunden werden.</div>')
      ];
    }
    $this->ViewJsonEncode($xResponse);
  }

  /**
   * Checks whether the current userId exists in the running restored files
   *
   * @return void
   */
  protected function CheckUserInDump()
  {
    $xData = null;
    $bStatus = false;
    $iUserId = (int)$this->app->User->GetID();
    $iBackupId = (int)$this->app->Secure->GetPOST('id');
    if(!empty($hData = $this->oBackupGateway->getBackupById($iBackupId)) && ($xData = $this->oBackupService->getDumpMetaData($hData['dateiname'], $this->app->Conf->WFuserdata))){
      // Check user admins
      $bStatus = !empty($xData) && array_key_exists('users', $xData) && in_array($iUserId, $xData['users']);
    }

    $message = $bStatus === true ? "Achtung: Es existieren neuere Datensicherungen. Möchten Sie wirklich alle bisherigen Einstellungen löschen/zurücksetzen?\n\nAlle nach diesem Zeitpunkt getätigten Einstellungen und Importvorlagen gehen verloren."
      : "Achtung: Beim Einspielen des Backups könnten Sie nicht mehr im Stande sein, sich auf dem System anzumelden. Möchten Sie wirklich alle bisherigen Einstellungen löschen / zurücksetzen? Alle nach diesem Zeitpunkt getätigten Einstellungen und Importvorlagen gehen verloren.";

    $ps_message = $this->SystemHasRunningProcesses() === true ? 'Achtung: Es existieren laufenden Prozesse im System. Mit Ihrer Bestätigung, werden sie beendet.' : '';
    $this->ViewJsonEncode(['status' => $bStatus, 'message' => $message, 'ps_message' => $ps_message]);
  }

  /**
   * @return bool
   */
  protected function SystemHasRunningProcesses()
  {
    $xData = null;
    $bStatus = false;
    if($runningId = $this->app->DB->Select('SELECT id FROM prozessstarter WHERE aktiv=1 AND mutex=1 LIMIT 1')){
      $bStatus = true;
    }
    return $bStatus;
  }

}

