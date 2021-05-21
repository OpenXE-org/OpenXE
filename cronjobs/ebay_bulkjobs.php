<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
if(file_exists(dirname(__DIR__).'/xentral_autoloader.php'))
{
  include_once dirname(__DIR__).'/xentral_autoloader.php';
}
@date_default_timezone_set('Europe/Berlin');

include_once dirname(__DIR__).'/conf/main.conf.php';
include_once dirname(__DIR__).'/phpwf/plugins/class.mysql.php';

include_once dirname(__DIR__).'/phpwf/plugins/class.secure.php';
include_once dirname(__DIR__).'/phpwf/plugins/class.user.php';
include_once dirname(__DIR__).'/www/lib/imap.inc.php';
include_once dirname(__DIR__).'/www/lib/class.erpapi.php';

if(is_file(dirname(__DIR__).'/www/lib/class.erpapi_custom.php')){
  include_once dirname(__DIR__) . '/www/lib/class.erpapi_custom.php';
}
include_once dirname(__DIR__).'/www/lib/class.httpclient.php';
$aes = '';
$phpversion = PHP_VERSION;
if(strpos($phpversion,'7') === 0 && (int)$phpversion{2} > 0)
{
  $aes = '2';
}
if($aes === '2' && is_file(dirname(__DIR__).'/www/lib/class.aes'.$aes.'.php'))
{
  include_once dirname(__DIR__).'/www/lib/class.aes'.$aes.'.php';
}elseif(is_file(dirname(__DIR__) . '/www/lib/class.aes.php')){
  include_once dirname(__DIR__) . '/www/lib/class.aes.php';
}
include_once dirname(__DIR__).'/www/lib/class.remote.php';

if(!class_exists('app_t')){
  class app_t extends ApplicationCore {
    public $DB;
    public $user;
    public $mail;
    public $erp;
    public $remote;
  }
}

if(empty($app)){
  $conf = new Config();
  $app = new app_t($conf);
  $app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass,null,$conf->WFdbport);
  $app->Conf = $conf;
  $app->erp = new erpAPI($app);
  $app->remote = new Remote($app);
  $app->Secure = new Secure($app);
}
if(!is_file(dirname(__DIR__).'/www/pages/shopimporter_ebay.php') || !$app->erp->ModulVorhanden('shopimporter_ebay')) {
  return;
}

$app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter + 1 WHERE mutex = 1 AND (parameter = 'ebay_bulkjobs' ) AND aktiv = 1");
if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE mutex = 0 AND (parameter = 'ebay_bulkjobs') AND aktiv = 1")) {
  return;
}

$query = "SELECT id FROm shopexport WHERE modulename='shopimporter_ebay' AND aktiv=1 AND lagerexport=1";
$shopIds = $app->DB->SelectArr($query);
if(empty($shopIds)) {
  return;
}

foreach ($shopIds as $shopId){
  $shopId = $shopId['id'];
  /** @var Shopimporter_Ebay $importer */
  $importer = $app->loadModule('shopimporter_ebay', true);
  $importer->getKonfig($shopId, '');
  if($importer->isThrottled()) {
    continue;
  }

  do{
    $jobIdInternal = '';
    $lookForNextJob = false;
    $updateJobInDatabase = false;

    $query = sprintf(
      "SELECT `id`, `uuid`, `job_id`, `file_id`, `status`, `next_action`, `notes`, `type` 
      FROM `ebay_bulk_jobs` 
      WHERE `status` NOT IN ('%s', '%s', '%s', '%s') AND `shop_id` = %d 
      ORDER BY `created_at` ASC LIMIT 1",
      'Completed','Failed', 'Aborted', 'Error', $shopId
    );
    $activeJob = $app->DB->SelectRow($query);

    if(empty($activeJob)){
      break;
    }

    $jobIdInternal = $activeJob['id'];
    $jobIdExternal = $activeJob['job_id'];
    $fileReferenceId = $activeJob['file_id'];
    $jobStatus = $activeJob['status'];
    $jobType = $activeJob['type'];
    $nextAction = $activeJob['next_action'];
    $uuid = $activeJob['uuid'];
    $notes = $activeJob['notes'];

    if(empty($jobIdExternal)){
      break;
    }

    $response = $importer->getBulkJobStatus($jobIdExternal);
    if((string)$response->ack === 'Success'){
      $jobStatus = (string)$response->jobProfile->jobStatus;

      $updateJobInDatabase = true;
      $finalAction = 'Undefined';
      $responseFileId = '';
      $notes = '';
      switch ($jobStatus){
        case 'Completed':
          $lookForNextJob = true;
          $notes = 'Errorcount: '.$response->jobProfile->errorCount;
          $responseFileId = (string)$response->jobProfile->fileReferenceId;
          $finalAction = 'None';
          break;
        case 'Aborted':
        case 'Failed':
        $lookForNextJob = true;
        $finalAction = 'None';
          break;
        case 'InProcess':
          $notes = 'Percent Complete: '.$response->jobProfile->percentComplete;
        case 'Scheduled':
          $finalAction='WaitForFinish';
          break;
        case 'Created':
          $updateJobInDatabase = false;
        default:
          break;
      }

      if($updateJobInDatabase){
        $query = sprintf(
          "UPDATE `ebay_bulk_jobs` 
          SET `status` = '%s', `next_action` = '%s', `response_file_id` = '%s', `notes` = '%s', 
              `last_updated_at` = NOW() 
          WHERE `id` = %d",
          $jobStatus, $finalAction, $responseFileId, $notes, $jobIdInternal
        );
        $app->DB->Update($query);
        if($jobStatus === 'Completed'){
          $importer->logStockResultsForFinishedJob($jobIdExternal,$fileReferenceId,$responseFileId);
        }

        if($finalAction === 'WaitForFinish'){
          break;
        }
      }
      continue;
    }

    if(!empty($notes)){
      $jobStatus = 'Error';
      $nextAction = 'Undefined';
    }
    $notes = $response->errorMessage->error->severity.': '.$response->errorMessage->error->message;

    $query = sprintf(
      "UPDATE `ebay_bulk_jobs` 
      SET `status` = '%s', `next_action` = '%s', 
        `notes` = '%s', `last_updated_at` = NOW() 
      WHERE `id` = %d",
      $jobStatus, $nextAction, $notes, $jobIdInternal
    );
    $app->DB->Update($query);
    $lookForNextJob = true;
  }while($lookForNextJob);

  if(empty($jobIdInternal)){
    continue;
  }

  $reasonForAbort = 'Undefined';

  if($nextAction === 'CreateJob'){
    if($importer->isThrottled('createBulkJob')) {
      continue;
    }
    $response = $importer->createBulkJob($jobType, $jobIdInternal,$uuid);
    $nextAction = $response['next_action'];
    $jobIdExternal = $response['jobId'];
    $fileReferenceId = $response['fileReferenceId'];
    $reasonForAbort = 'Unerwarteten Job gefunden';
  }

  if($nextAction === 'UploadFile'){
    $gatheredRequests = $importer->createBulkRequests($jobType);
    if(empty($gatheredRequests)){
      $nextAction = 'AbortJob';
      $reasonForAbort = 'Keine Artikel zum Synchronisieren vorhanden';
    }else{
      $response = $importer->uploadBulkFile($jobIdExternal,$fileReferenceId,$gatheredRequests);
      $nextAction = $response['next_action'];
      $reasonForAbort = 'Datei kann nicht hochgeladen werden';
    }
  }

  if($nextAction === 'StartJob'){
    $response = $importer->startBulkJob($jobIdExternal);
    $nextAction = $response['next_action'];
    $reasonForAbort = 'Job kann nicht gestartet werden';
  }

  if($nextAction === 'AbortJob' ){
    if(!empty($jobIdExternal)){
      $response = $importer->abortBulkJob($jobIdInternal, $jobIdExternal, $reasonForAbort);
    }elseif(empty($fileReferenceId)){
      $query = sprintf("UPDATE ebay_bulk_jobs SET status='Error', notes = 'Fehlerhafte Dateireferenz' WHERE id=%d",
        $jobIdInternal);
      $app->DB->UPDATE($query);
    }
  }
  $app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE (parameter = 'ebay_bulkjobs' ) AND aktiv = 1");
}

$app->DB->Update("UPDATE prozessstarter SET mutex = 0, mutexcounter = 0, letzteausfuerhung = now() WHERE (parameter = 'ebay_bulkjobs' ) AND aktiv = 1");
