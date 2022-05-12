<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
include_once dirname(__DIR__).'/conf/main.conf.php';
include_once dirname(__DIR__).'/phpwf/plugins/class.mysql.php';
include_once dirname(__DIR__).'/www/lib/imap.inc.php';
include_once dirname(__DIR__).'/www/lib/class.erpapi.php';
if(file_exists(dirname(__DIR__).'/www/lib/class.erpapi_custom.php')){
  include_once(dirname(__DIR__).'/www/lib/class.erpapi_custom.php');
}
include_once dirname(__DIR__).'/www/lib/class.remote.php';
if(file_exists(dirname(__DIR__).'/www/lib/class.remote_custom.php')){
  include_once dirname(__DIR__).'/www/lib/class.remote_custom.php' ;
}
include_once dirname(__DIR__).'/www/lib/class.httpclient.php';
if(!class_exists('AES')){
  $aes = '';
  $phpversion = PHP_VERSION;
  if(strpos($phpversion,'7') ===0 && (int)$phpversion{2} > 0) {
    $aes = '2';
  }
  if($aes === '2' && is_file(dirname(__DIR__) . '/www/lib/class.aes' . $aes . '.php')){
    include_once dirname(__DIR__) . '/www/lib/class.aes' . $aes . '.php';
  }else{
    include_once dirname(__DIR__) . '/www/lib/class.aes.php' ;
  }
}
include_once dirname(__DIR__).'/www/pages/shopimport.php';
include_once dirname(__DIR__).'/www/plugins/phpmailer/class.phpmailer.php';
include_once dirname(__DIR__).'/www/plugins/phpmailer/class.smtp.php';
include_once dirname(__DIR__).'/phpwf/plugins/class.secure.php';

if(!class_exists('app_t'))
{
  class app_t {
    var $DB;
    var $erp;
    var $user;
    var $remote;
  }
}

if(!class_exists('User'))
{
  class User
  {
    function __construct($app)
    {
      $this->app = $app;
    }
    
    function GetName()
    {
      return 'Cronjob';
    }
    
    function GetID()
    {
      return 0;
    }
    
    function GetFirma()
    {
      return 1;
    }
    
    function GetType()
    {
      return 'admin';
    }
    
    function DefaultProjekt()
    {
      return $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='1' LIMIT 1");
    }
    
    function GetAdresse()
    {
      return 0;
    }
    
    function GetUsername()
    {
      return 'Cronjob';
    }
    
  }
  
}

//ENDE

if(empty($app) || !class_exists('ApplicationCore') || !($app instanceof ApplicationCore)) {
  $app = new app_t();
}

if(empty($app->Conf)) {
  $conf = new Config();
  $app->Conf = $conf;
}
if(empty($app->DB) || empty($app->DB->connection)) {
  $app->DB = new DB($app->Conf->WFdbhost, $app->Conf->WFdbname, $app->Conf->WFdbuser, $app->Conf->WFdbpass, $app, $app->Conf->WFdbport);
}

if(empty($app->erp)) {
  if(class_exists('erpAPICustom')) {
    $erp = new erpAPICustom($app);
  }
  else {
    $erp = new erpAPI($app);
  }
  $app->erp = $erp;
}

if(class_exists('RemoteCustom'))
{
  $remote = new RemoteCustom($app);
}else{
  $remote = new Remote($app);
}

$app->remote = $remote;
$app->Secure = new Secure($app);
$app->User = new User($app);

$app->DB->Update(
  "UPDATE prozessstarter SET mutexcounter = mutexcounter + 1 WHERE mutex = 1 AND parameter = 'cleaner'  AND aktiv = 1"
);
if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE mutex = 0 AND parameter = 'cleaner' AND aktiv = 1")) {
  return;
}
usleep(mt_rand(10000,200000));
if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE mutex = 0 AND parameter = 'cleaner' AND aktiv = 1")) {
  return;
}
$app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(),mutex=1,mutexcounter=0 WHERE parameter = 'cleaner'");

$free = !function_exists('disk_free_space')?false:disk_free_space(dirname(__DIR__));
$tables = [
  'shopexport_log',
  'logfile',
  'protokoll',
  'logfile',
  'cronjob_log',
  'cronjob_starter_running',
  'uebertragungen_monitor',
  'uebertragungen_dateien',
  'shopimporter_amazon_requestinfo',
  'adapterbox_request_log',
  'userkonfiguration',
  'templatemessage',
  'shopimport_auftraege',
  'versandzentrum_log',
  'api_request_response_log',
  'shopimport_amazon_fees',
];
$minMemoryMb = 1;
$minMemory = $minMemoryMb * 1024 * 1024;


$app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(),mutex=1,mutexcounter=0 WHERE parameter = 'cleaner'");
$diskFreeId = 0;
if($free !== false) {
  $diskFree = (int)floor($free / 1024);
  $app->DB->Insert(
    sprintf(
      'INSERT INTO `system_disk_free` (`created_at`,`disk_free_kb_start`) VALUES (NOW(),%d)',
      $diskFree
    )
  );
  $diskFreeId = $app->DB->GetInsertID();
}

$tableSchemas = $app->DB->SelectArr(
  sprintf(
    "SELECT 
       table_name AS `Table`, TABLE_ROWS AS `rows`, AVG_ROW_LENGTH AS `per_row`,
       round((data_length + index_length) / 1024 / 1024) AS `Size`,
       round(DATA_FREE/ 1024 / 1024) AS `free`, DATA_FREE / (data_length + index_length) AS `factor`
    FROM information_schema.TABLES 
    WHERE table_schema = '%s'
    AND (data_length + index_length + DATA_FREE) > 1024 * 1024
    AND (
          (round(DATA_FREE/ 1024 / 1024) > 5 AND DATA_FREE / (data_length + index_length) > 0.1)    
          OR table_name IN ('%s')
          OR (TABLE_ROWS = 0 AND round((data_length + index_length) / 1024 / 1024) > 10)
          OR (AVG_ROW_LENGTH > 1000000 AND table_name IN ('device_jobs'))
     )
    ORDER BY
         TABLE_ROWS = 0 AND round((data_length + index_length) / 1024 / 1024) > 10 DESC,
         AVG_ROW_LENGTH > 1000000 DESC,
         DATA_FREE / (data_length + index_length) > 10 DESC,
         DATA_FREE / (data_length + index_length) > 5 DESC,
         round(DATA_FREE/ 1024 / 1024) DESC ",
    $app->Conf->WFdbname, implode("', '", $tables)
  )
);
$nonLogTablesOptimized = 10;
$maxMbPerTable = 1024;
if($diskFree !== false && $diskFree < 2048 * 1024) {
  $maxMbPerTable = 512;
}

$optimize = [];
$tableSchemaByTables = [];
if(!empty($tableSchemas)) {
  foreach($tableSchemas as $tableInfo) {
    $tableInfo['todelete'] = 0;
    if($tableInfo['Size'] > $maxMbPerTable) {
      $tableInfo['todelete'] = ceil(($tableInfo['Size'] - $maxMbPerTable) * 1024 * 1024 / $tableInfo['per_row']);
      if($tableInfo['todelete'] > ceil($tableInfo['rows'] / 2)) {
        $tableInfo['todelete'] = ceil($tableInfo['rows'] / 2);
      }
    }
    $tableSchemaByTables[$tableInfo['Table']] = $tableInfo;
    $hasNoRowsAndBigSize = ($tableInfo['rows'] == 0 && $tableInfo['Size'] >= 10) || $tableInfo['per_row'] > 1000000;
    $isTableLogFile = in_array($tableInfo['Table'], $tables);
    if($tableInfo['Size'] + $tableInfo['free'] > 2 * $minMemoryMb) {
      $optimize[$tableInfo['Table']] =  $tableInfo['Size'] + $tableInfo['free'];
    }
    $isFreeableSpaceGreatEnough = $tableInfo['free'] > 5 && ($tableInfo['free'] > 10 || $tableInfo['factor'] > 10);
    if($hasNoRowsAndBigSize  || (!$isTableLogFile && $isFreeableSpaceGreatEnough)) {
      $nonLogTablesOptimized--;
      if($nonLogTablesOptimized > 0) {
        $app->DB->Query(sprintf('OPTIMIZE TABLE `%s`', $tableInfo['Table']));
        $app->DB->Update(
          "UPDATE `prozessstarter` 
          SET `letzteausfuerhung` = NOW(), `mutex` = 1, `mutexcounter` = 0 
          WHERE `parameter` = 'cleaner' AND `aktiv` = 1"
        );
      }
    }
  }
}
else {
  $optimize = $free < $minMemory?[]:$app->DB->SelectPairs(
    sprintf(
      "SELECT 
       table_name AS `Table`, 
       '1' AS `Size` 
      FROM information_schema.TABLES 
      WHERE table_schema = '%s' AND table_name IN (%s)
      AND (data_length + index_length) > %d
      AND (data_length + index_length) < %d",
      $app->Conf->WFdbname, "'".implode("', '", $tables)."'",
      $minMemory, $free
    )
  );
}

$app->DB->Delete('DELETE FROM templatemessage WHERE zeitstempel < DATE_SUB(now(), INTERVAL 1 DAY)');
if($app->DB->affected_rows() > 0 && !empty($optimize['templatemessage'])) {
  $app->DB->Query('OPTIMIZE TABLE `templatemessage`');
}

$app->DB->Delete(
  "DELETE uk 
  FROM `userkonfiguration` AS uk
  LEFT JOIN `useronline` AS uo ON uk.user = uo.user_id AND uo.login = 1 
  WHERE uk.name LIKE 'tablesearch\\_%' AND ISNULL(uo.user_id)"
);
if($app->DB->affected_rows() > 0 && !empty($optimize['userkonfiguration'])) {
  $app->DB->Query('OPTIMIZE TABLE `userkonfiguration`');
  unset($optimize['userkonfiguration']);
}
$affectedRows = 0;
if($app->erp->Firmendaten('cleaner_logfile') && ($tage = (int)$app->erp->Firmendaten('cleaner_logfile_tage')) > 0) {
  $app->DB->Delete(
    sprintf(
      "DELETE FROM logfile WHERE DATE_SUB(CURDATE(), INTERVAL %d DAY) >= datum OR datum = '0000-00-00'",
      $tage
    )
  );
  $affectedRows = $app->DB->affected_rows();
  if(!empty($tableSchemaByTables['logfile']) && ($tableSchemaByTables['logfile']['todelete'] > 0)) {
    $tableSchemaByTables['logfile']['todelete'] -= $affectedRows;
  }
  if($affectedRows > 0 && !empty($optimize['logfile'])) {
    if(!empty($tableSchemaByTables['logfile']) && ($tableSchemaByTables['logfile']['todelete'] > 0)) {
      $minId = $app->DB->Select('SELECT MIN(`id`) FROM `logfile`');
      $toDelete = $tableSchemaByTables['logfile']['todelete'];
      if($toDelete > 100000) {
        $toDelete = 100000;
      }
      $app->DB->Delete(sprintf('DELETE FROM `logfile` WHERE `id` <= %d', $minId + $toDelete));
      $affectedRows = $app->DB->affected_rows();
      $tableSchemaByTables['logfile']['todelete'] -= $affectedRows;
    }
    $app->DB->Query('OPTIMIZE TABLE `logfile`');
    unset($optimize['logfile']);
  }
  $app->DB->Update(
    "UPDATE prozessstarter SET letzteausfuerhung=NOW(),mutex=1,mutexcounter=0 WHERE parameter = 'cleaner' AND aktiv = 1"
  );
  $app->DB->Delete(
    sprintf(
      "DELETE FROM `api_request_response_log` 
      WHERE DATE_SUB(NOW(), INTERVAL %d DAY) >= `created_at` OR `created_at` = '0000-00-00'",
      $tage
    )
  );
  if($app->DB->affected_rows() > 0 && !empty($optimize['api_request_response_log'])) {
    $app->DB->Query('OPTIMIZE TABLE `api_request_response_log`');
  }
  $app->DB->Update(
    "UPDATE `prozessstarter` 
    SET `letzteausfuerhung` = NOW(), `mutex` = 1, `mutexcounter` = 0 
    WHERE `parameter` = 'cleaner' AND `aktiv` = 1"
  );
  $app->DB->Delete(
    sprintf(
      "DELETE FROM `adapterbox_request_log` WHERE DATE_SUB(CURDATE(), INTERVAL %d DAY) >= `created_at`",
      $tage
    )
  );
  $affectedRows = $app->DB->affected_rows();
  if(!empty($tableSchemaByTables['adapterbox_request_log'])
    && ($tableSchemaByTables['adapterbox_request_log']['todelete'] > 0)) {
    $tableSchemaByTables['adapterbox_request_log']['todelete'] -= $affectedRows;
  }
  if($affectedRows > 0 && !empty($optimize['adapterbox_request_log'])) {
    if(!empty($tableSchemaByTables['adapterbox_request_log'])
      && ($tableSchemaByTables['adapterbox_request_log']['todelete'] > 0)) {
      $minId = $app->DB->Select('SELECT MIN(`id`) FROM `adapterbox_request_log`');
      $toDelete = $tableSchemaByTables['adapterbox_request_log']['todelete'];
      if($toDelete > 100000) {
        $toDelete = 100000;
      }
      $app->DB->Delete(sprintf('DELETE FROM `adapterbox_request_log` WHERE `id` <= %d', $minId + $toDelete));
      $affectedRows = $app->DB->affected_rows();
      $tableSchemaByTables['adapterbox_request_log']['todelete'] -= $affectedRows;
    }
  }
  $app->DB->Update(
    "UPDATE prozessstarter SET letzteausfuerhung=NOW(),mutex=1,mutexcounter=0 WHERE parameter = 'cleaner' AND aktiv = 1"
  );
}

$adapterBoxLogs = $app->DB->SelectRow(
  'SELECT MIN(`id`) AS `minid`, COUNT(`id`) AS `co` FROM `adapterbox_request_log`'
);
$timeout = 10;
while($adapterBoxLogs['co'] > 500000) {
  $timeout--;
  if($timeout <= 0) {
    break;
  }
  $app->DB->Delete(
    sprintf(
      "DELETE FROM `adapterbox_request_log` 
      WHERE `id` < %d AND `created_at` < DATE_SUB(CURDATE(), INTERVAL %d DAY)",
      $adapterBoxLogs['minid'] + 250000, 3
    )
  );
  $adapterBoxLogs['co'] -= 250000;
  $adapterBoxLogs['minid'] += 250000;
  $tmpAffectedRows = (int)$app->DB->affected_rows();
  $affectedRows += $tmpAffectedRows;
  $app->DB->Update(
    "UPDATE `prozessstarter` 
    SET `letzteausfuerhung`=NOW(),`mutex`=1,`mutexcounter`=0 
    WHERE `parameter` = 'cleaner' AND `aktiv` = 1"
  );
  if($tmpAffectedRows < 250000) {
    break;
  }
}

if($affectedRows > 0 && !empty($optimize['adapterbox_request_log'])) {
  $app->DB->Query('OPTIMIZE TABLE `adapterbox_request_log`');
  unset($optimize['adapterbox_request_log']);
}
$affectedRows = 0;

if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE parameter = 'cleaner' AND aktiv = 1")) {
  return;
}
if($app->erp->Firmendaten('cleaner_protokoll') && ($tage = (int)$app->erp->Firmendaten('cleaner_protokoll_tage')) > 0) {
  $app->DB->Delete(
    sprintf(
      "DELETE FROM `protokoll` WHERE DATE_SUB(CURDATE(), INTERVAL %d DAY) >= `datum` OR `datum` = '0000-00-00 00:00:00'",
      $tage
    )
  );
  $affectedRows = $app->DB->affected_rows();
  if(!empty($tableSchemaByTables['protokoll'])
    && ($tableSchemaByTables['protokoll']['todelete'] > 0)) {
    $tableSchemaByTables['protokoll']['todelete'] -= $affectedRows;
  }
  if($affectedRows > 0 && !empty($optimize['protokoll'])) {
    if(!empty($tableSchemaByTables['protokoll'])
      && ($tableSchemaByTables['protokoll']['todelete'] > 0)) {
      $minId = $app->DB->Select('SELECT MIN(`id`) FROM `protokoll`');
      $toDelete = $tableSchemaByTables['protokoll']['todelete'];
      if($toDelete > 100000) {
        $toDelete = 100000;
      }
      $app->DB->Delete(sprintf('DELETE FROM `protokoll` WHERE `id` <= %d', $minId + $toDelete));
      $affectedRows = $app->DB->affected_rows();
      $tableSchemaByTables['protokoll']['todelete'] -= $affectedRows;
    }
    $app->DB->Query('OPTIMIZE TABLE `protokoll`');
    unset($optimize['protokoll']);
  }
  $app->DB->Update(
    "UPDATE prozessstarter SET letzteausfuerhung=NOW(),mutex=1,mutexcounter=0 WHERE parameter = 'cleaner' AND aktiv = 1"
  );
}
if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE parameter = 'cleaner' AND aktiv = 1")) {
  return;
}
if($app->erp->Firmendaten('cleaner_shopimport') && ($tage = (int)$app->erp->Firmendaten('cleaner_shopimport_tage')) > 0) {
  $app->DB->Delete(
    sprintf(
      "DELETE FROM `shopimport_auftraege` 
      WHERE DATE_SUB(CURDATE(), INTERVAL %d DAY) >= logdatei OR logdatei = '0000-00-00'",
      $tage
    )
  );
  $affectedRows = $app->DB->affected_rows();
  if($affectedRows > 0 && !empty($optimize['shopimport_auftraege'])) {
    $app->DB->Query('OPTIMIZE TABLE `shopimport_auftraege`');
    unset($optimize['shopimport_auftraege']);
  }
  $app->DB->Update(
    "UPDATE prozessstarter SET letzteausfuerhung=NOW(),mutex=1,mutexcounter=0 WHERE parameter = 'cleaner' AND aktiv = 1"
  );
}
if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE parameter = 'cleaner' AND aktiv = 1")) {
  return;
}
if($app->erp->Firmendaten('cleaner_versandzentrum')
  && ($tage = (int)$app->erp->Firmendaten('cleaner_versandzentrum_tage')) > 0
) {
  $app->DB->Delete(
    sprintf(
      "DELETE FROM `versandzentrum_log` 
      WHERE DATE_SUB(CURDATE(), INTERVAL %d DAY) >= `zeitstempel` OR `zeitstempel` = '0000-00-00 00:00:00'",
      $tage
    )
  );
  $affectedRows = $app->DB->affected_rows();
  if(!empty($tableSchemaByTables['versandzentrum_log'])
    && ($tableSchemaByTables['versandzentrum_log']['todelete'] > 0)) {
    $tableSchemaByTables['versandzentrum_log']['todelete'] -= $affectedRows;
  }
  if($affectedRows > 0 && !empty($optimize['versandzentrum_log'])) {
    if(!empty($tableSchemaByTables['versandzentrum_log'])
      && ($tableSchemaByTables['versandzentrum_log']['todelete'] > 0)) {
      $minId = $app->DB->Select('SELECT MIN(`id`) FROM `versandzentrum_log`');
      $toDelete = $tableSchemaByTables['versandzentrum_log']['todelete'];
      if($toDelete > 100000) {
        $toDelete = 100000;
      }
      $app->DB->Delete(sprintf('DELETE FROM `versandzentrum_log` WHERE `id` <= %d', $minId + $toDelete));
      $affectedRows = $app->DB->affected_rows();
      $tableSchemaByTables['versandzentrum_log']['todelete'] -= $affectedRows;
    }
    $app->DB->Query('OPTIMIZE TABLE `versandzentrum_log`');
    unset($optimize['versandzentrum_log']);
  }
  $app->DB->Update(
    "UPDATE prozessstarter SET letzteausfuerhung=NOW(),mutex=1,mutexcounter=0 WHERE parameter = 'cleaner' AND aktiv = 1"
  );
}
if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE parameter = 'cleaner' AND aktiv = 1")) {
  return;
}

$shopExportLogCleanerActive = $app->erp->Firmendaten('cleaner_shopexportlog');
$tage = (int)$app->erp->Firmendaten('cleaner_shopexportlog_tage');
$isToDelete = !empty($tableSchemaByTables['shopexport_log'])
  && !empty($tableSchemaByTables['shopexport_log']['todelete']);
if((!$shopExportLogCleanerActive || $tage <= 0 || $tage > 30) && $isToDelete) {
  $shopExportLogCleanerActive = true;
  if($tage <= 0 || $tage > 30) {
    $tage = 30;
  }
}
if($shopExportLogCleanerActive && $tage > 0) {
  $firstEntry = $app->DB->SelectRow(
    sprintf(
      'SELECT `id`, `zeitstempel`, DATE_SUB(CURDATE(), INTERVAL %d DAY) >= `zeitstempel` AS `expired`
      FROM `shopexport_log` 
      ORDER BY `id` 
      LIMIT 1',
      $tage
    )
  );
  $entryToDelete = null;
  $affectedRows = 0;
  for($i = 0; $i < 10 && !empty($firstEntry['expired']); $i++) {
    $app->DB->Delete(
      sprintf(
        "DELETE FROM `shopexport_log` 
        WHERE `id` < %d AND DATE_SUB(CURDATE(), INTERVAL %d DAY) >= `zeitstempel`",
        $firstEntry['id'] + 50000, $tage
      )
    );
    $affectedRows += $app->DB->affected_rows();
    $firstEntry = $app->DB->SelectRow(
      sprintf(
        'SELECT `id`, `zeitstempel`, DATE_SUB(CURDATE(), INTERVAL %d DAY) >= `zeitstempel` AS `expired`
        FROM `shopexport_log` 
        ORDER BY `id` 
        LIMIT 1',
        $tage
      )
    );
    $app->DB->Update(
      "UPDATE `prozessstarter` 
      SET `letzteausfuerhung` = NOW(), `mutex` = 1, `mutexcounter` = 0 
      WHERE `parameter` = 'cleaner' AND `aktiv` = 1"
    );
  }

  if(!empty($tableSchemaByTables['shopexport_log'])
    && ($tableSchemaByTables['shopexport_log']['todelete'] > 0)) {
    $tableSchemaByTables['shopexport_log']['todelete'] -= $affectedRows;
  }
  if($affectedRows > 0 && !empty($optimize['shopexport_log'])) {
    if(!empty($tableSchemaByTables['shopexport_log'])
      && ($tableSchemaByTables['shopexport_log']['todelete'] > 0)) {
      $minId = $app->DB->Select('SELECT MIN(`id`) FROM `shopexport_log`');
      $toDelete = $tableSchemaByTables['shopexport_log']['todelete'];
      if($toDelete > 100000) {
        $toDelete = 100000;
      }
      $app->DB->Delete(sprintf('DELETE FROM `shopexport_log` WHERE `id` <= %d', $minId + $toDelete));
      $affectedRows = $app->DB->affected_rows();
      $tableSchemaByTables['shopexport_log']['todelete'] -= $affectedRows;
    }
    $app->DB->Query('OPTIMIZE TABLE `shopexport_log`');
    unset($optimize['shopexport_log']);
  }
  $app->DB->Update(
    "UPDATE prozessstarter SET letzteausfuerhung=NOW(),mutex=1,mutexcounter=0 WHERE parameter = 'cleaner' AND aktiv = 1"
  );
  if($tage < 14) {
    $tage = 14;
  }
  $app->DB->Delete(
    sprintf(
      "DELETE FROM `shopimporter_amazon_requestinfo` 
      WHERE DATE_SUB(CURDATE(), INTERVAL %d DAY) >= `created_at` OR `created_at` = '0000-00-00 00:00:00'",
      $tage
    )
  );

  $affectedRows = $app->DB->affected_rows();
  $cRequestInfos = (int)$app->DB->Select('SELECT COUNT(`id`) FROM `shopimporter_amazon_requestinfo`');
  if($cRequestInfos > 50000) {
    $timeInventoryStart = microtime(true);
    $fillInventories = $app->DB->SelectArr(
      "SELECT `shop_id`, `parameter`, MIN(`id`) AS `minid`, MAX(`id`) AS `maxid`, COUNT(`id`) AS `countid` 
      FROM `shopimporter_amazon_requestinfo` 
      WHERE `status` = 'success' and `doctype` = 'auftrag' AND `type` = 'FillInventory'
      GROUP BY `parameter`, `shop_id` 
      HAVING COUNT(`id`) > 500
      ORDER BY COUNT(`id`) DESC
      LIMIT 250"
    );
    if(empty($fillInventories)) {
      $fillInventories = [];
    }
    foreach($fillInventories as $fillInventory) {
      $requestIds = $app->DB->SelectPairs(
        sprintf(
          "SELECT `id`, `shopimporter_amazon_aufrufe_id` 
          FROM `shopimporter_amazon_requestinfo`
          WHERE `shop_id` = %d AND `status` = 'success' and `doctype` = 'auftrag' AND `type` = 'FillInventory'
           AND `parameter` = '%s' AND `id` >= %d AND `id` <= %d
          ORDER BY `created_at`
          LIMIT %d",
          $fillInventory['shop_id'], $fillInventory['parameter'],
          $fillInventory['minid'],
          $fillInventory['maxid'],
          $fillInventory['countid'] - 500
        )
      );

      if(!empty($requestIds)) {
        $app->DB->Delete(
          sprintf(
            "DELETE FROM `shopimporter_amazon_requestinfo` WHERE `id` IN (%s)",
            implode(',', array_keys($requestIds))
          )
        );
        $affectedRows2 = $app->DB->affected_rows();
        if($affectedRows2 > 0){
          $affectedRows += $affectedRows2;
        }
        $requestIds = array_diff(array_unique($requestIds), [0]);
        if(!empty($requestIds)){
          $app->DB->Delete(
            sprintf(
              "DELETE FROM `shopimport_amazon_aufrufe` 
              WHERE `id` IN (%s) AND `abgeschlossen` = 1 AND `funktion` = 'FillInventory' AND `shopid` = %d",
              implode(',', $requestIds), $fillInventory['shop_id']
            )
          );
        }
        unset($requestIds);
      }
      if(microtime(true) - $timeInventoryStart > 120) {
        break;
      }
    }

    unset($fillInventories);
    $app->DB->Update(
      "UPDATE `prozessstarter` 
      SET `letzteausfuerhung`=NOW(),`mutex`=1,`mutexcounter`=0 
      WHERE `parameter` = 'cleaner' AND `aktiv` = 1"
    );
  }

  if($affectedRows > 0 && !empty($optimize['shopimporter_amazon_requestinfo'])) {
    $app->DB->Query('OPTIMIZE TABLE `shopimporter_amazon_requestinfo`');
    unset($optimize['shopimporter_amazon_requestinfo']);
  }
  $app->DB->Update(
    "UPDATE prozessstarter SET letzteausfuerhung=NOW(),mutex=1,mutexcounter=0 WHERE parameter = 'cleaner' AND aktiv = 1"
  );
}

$shopExportLogCleanerActive = $app->erp->Firmendaten('cleaner_shopexportlog');
$tage = (int)$app->erp->Firmendaten('cleaner_shopexportlog_tage');
$isToDelete = !empty($tableSchemaByTables['shopimport_amazon_fees'])
  && !empty($tableSchemaByTables['shopimport_amazon_fees']['todelete']);
if((!$shopExportLogCleanerActive || $tage <= 0 || $tage > 30) && $isToDelete) {
  $shopExportLogCleanerActive = true;
  if($tage <= 0 || $tage > 30) {
    $tage = 30;
  }
}
if($shopExportLogCleanerActive && $tage > 0) {
  if($tage < 30) {
    $tage = 30;
  }
  $app->DB->Delete(
    sprintf(
      "DELETE FROM `shopimport_amazon_fees` 
      WHERE DATE_SUB(NOW(), INTERVAL %d DAY) >= `zeitstempel` OR `zeitstempel` = '0000-00-00 00:00:00'",
      $tage
    )
  );
  $affectedRows = $app->DB->affected_rows();
  if(!empty($tableSchemaByTables['shopimport_amazon_fees'])
    && ($tableSchemaByTables['shopimport_amazon_fees']['todelete'] > 0)) {
    $tableSchemaByTables['shopimport_amazon_fees']['todelete'] -= $affectedRows;
  }
  if($affectedRows > 0 && !empty($optimize['shopimport_amazon_fees'])) {
    $app->DB->Query('OPTIMIZE TABLE `shopimport_amazon_fees`');
    unset($optimize['shopimport_amazon_fees']);
  }
  $app->DB->Update(
    "UPDATE prozessstarter SET letzteausfuerhung=NOW(),mutex=1,mutexcounter=0 WHERE parameter = 'cleaner' AND aktiv = 1"
  );
}

if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE parameter = 'cleaner' AND aktiv = 1")) {
  return;
}
if($app->erp->Firmendaten('cleaner_uebertragungen')
  && ($tage = (int)$app->erp->Firmendaten('cleaner_uebertragungen_tage')) > 0
) {
  $app->DB->Delete(
    sprintf(
      "DELETE FROM `uebertragungen_monitor` 
      WHERE DATE_SUB(CURDATE(), INTERVAL %d DAY) >= `zeitstempel` OR `zeitstempel` = '0000-00-00'",
      $tage
    )
  );
  $affectedRows = $app->DB->affected_rows();
  if($affectedRows > 0 && !empty($optimize['uebertragungen_monitor'])) {
    $app->DB->Query('OPTIMIZE TABLE `uebertragungen_monitor`');
    unset($optimize['uebertragungen_monitor']);
  }
  $app->DB->Update(
    "UPDATE prozessstarter SET letzteausfuerhung=NOW(),mutex=1,mutexcounter=0 WHERE parameter = 'cleaner' AND aktiv = 1"
  );
  if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE parameter = 'cleaner' AND aktiv = 1")) {
    return;
  }
  $query = $app->DB->Query(
    sprintf(
      "SELECT id, datei_wawi 
      FROM `uebertragungen_dateien` 
      WHERE zeitstempel < DATE_SUB(NOW(), INTERVAL %d DAY) 
      LIMIT 1000",
      $tage
    )
  );
  if(!empty($query)) {
    $userdata = rtrim($app->Conf->WFuserdata,'/').'/';
    $cAffected = 0;
    while($row = $app->DB->Fetch_Assoc($query)) {
      if(!empty($row['datei_wawi'])) {
        if(is_file($row['datei_wawi']) && strpos($row['datei_wawi'],$userdata)===0) {
          @unlink($row['datei_wawi']);
        }
        $app->DB->Delete(
          sprintf(
            'DELETE FROM `uebertragungen_dateien` WHERE id = %d',
            $row['id']
          )
        );
        $app->DB->Delete(
          sprintf(
            'DELETE FROM `uebertragungen_monitor` WHERE datei = %d',
            $row['id']
          )
        );
        $cAffected++;
      }
    }
    $app->DB->free($query);
    if($cAffected > 0 && !empty($optimize['uebertragungen_dateien'])) {
      $app->DB->Query('OPTIMIZE TABLE `uebertragungen_dateien`');
      unset($optimize['uebertragungen_dateien']);
    }
    if($cAffected > 0 && !empty($optimize['uebertragungen_monitor'])) {
      $app->DB->Query('OPTIMIZE TABLE `uebertragungen_monitor`');
      unset($optimize['uebertragungen_monitor']);
    }
  }
  $app->DB->Update(
    "UPDATE prozessstarter SET letzteausfuerhung=NOW(),mutex=1,mutexcounter=0 WHERE parameter = 'cleaner' AND aktiv = 1"
  );
  if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE parameter = 'cleaner' AND aktiv = 1")) {
    return;
  }
}

$adapterBoxCleanerActive = $app->erp->Firmendaten('cleaner_adapterbox');
$tage = (int)$app->erp->Firmendaten('cleaner_adapterbox_tage');
$isToDelete = (
  !empty($tableSchemaByTables['adapterbox_log'])
    && !empty($tableSchemaByTables['adapterbox_log']['todelete'])
  )
  || (
    !empty($tableSchemaByTables['adapterbox_request_log'])
    && !empty($tableSchemaByTables['adapterbox_request_log']['todelete'])
  );
if((!$adapterBoxCleanerActive || $tage <= 0 || $tage > 30) && $isToDelete) {
  $adapterBoxCleanerActive = true;
  if($tage <= 0 || $tage > 30) {
    $tage = 30;
  }
}

$affectedRows1 = 0;

if($adapterBoxCleanerActive && $tage > 0) {
  $app->DB->Delete(
    sprintf(
      "DELETE FROM `adapterbox_log` 
      WHERE DATE_SUB(CURDATE(), INTERVAL %d DAY) >= `datum` OR `datum` = '0000-00-00 00:00:00'",
      $tage
    )
  );
  $affectedRows1 = $app->DB->affected_rows();
  if(!empty($tableSchemaByTables['adapterbox_log'])
    && ($tableSchemaByTables['adapterbox_log']['todelete'] > 0)) {
    $tableSchemaByTables['adapterbox_log']['todelete'] -= $affectedRows;
  }
  if($affectedRows > 0 && !empty($optimize['adapterbox_log'])) {
    if(!empty($tableSchemaByTables['adapterbox_log'])
      && ($tableSchemaByTables['adapterbox_log']['todelete'] > 0)) {
      $minId = $app->DB->Select('SELECT MIN(`id`) FROM `adapterbox_log`');
      $toDelete = $tableSchemaByTables['adapterbox_log']['todelete'];
      if($toDelete > 100000) {
        $toDelete = 100000;
      }
      $app->DB->Delete(sprintf('DELETE FROM `adapterbox_log` WHERE `id` <= %d', $minId + $toDelete));
      $affectedRows1 += $app->DB->affected_rows();
      $tableSchemaByTables['adapterbox_log']['todelete'] -= $affectedRows;
    }
  }
  $app->DB->Delete(
    sprintf(
      "DELETE FROM `adapterbox_request_log` 
      WHERE DATE_SUB(CURDATE(), INTERVAL %d DAY) >= `created_at` OR `created_at` = '0000-00-00 00:00:00'",
      $tage
    )
  );
  $affectedRows = $app->DB->affected_rows();
  if(!empty($tableSchemaByTables['adapterbox_request_log'])
    && ($tableSchemaByTables['adapterbox_request_log']['todelete'] > 0)) {
    $tableSchemaByTables['adapterbox_request_log']['todelete'] -= $affectedRows;
  }
  if($affectedRows > 0 && !empty($optimize['adapterbox_request_log'])) {
    if(!empty($tableSchemaByTables['adapterbox_request_log'])
      && ($tableSchemaByTables['adapterbox_request_log']['todelete'] > 0)) {
      $minId = $app->DB->Select('SELECT MIN(`id`) FROM `adapterbox_log`');
      $toDelete = $tableSchemaByTables['adapterbox_request_log']['todelete'];
      if($toDelete > 100000) {
        $toDelete = 100000;
      }
      $app->DB->Delete(sprintf('DELETE FROM `adapterbox_request_log` WHERE `id` <= %d', $minId + $toDelete));
      $affectedRows = $app->DB->affected_rows();
      $tableSchemaByTables['adapterbox_request_log']['todelete'] -= $affectedRows;
    }
    $app->DB->Query('OPTIMIZE TABLE `adapterbox_request_log`');
    unset($optimize['adapterbox_request_log']);
  }
  $app->DB->Update(
    "UPDATE prozessstarter SET letzteausfuerhung=NOW(),mutex=1,mutexcounter=0 WHERE parameter = 'cleaner' AND aktiv = 1"
  );
  if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE parameter = 'cleaner' AND aktiv = 1")) {
    return;
  }
}

$adapterBoxLogs = $app->DB->SelectRow(
  'SELECT MIN(`id`) AS `minid`, COUNT(`id`) AS `co` FROM `adapterbox_log`'
);
if($adapterBoxLogs['co'] > 500000) {
  $app->DB->Delete(
    sprintf(
      "DELETE FROM `adapterbox_log` 
      WHERE `id` < %d AND `datum` < DATE_SUB(CURDATE(), INTERVAL %d DAY)",
      $adapterBoxLogs['minid'] + 250000, 3
    )
  );
  $affectedRows1 += (int)$app->DB->affected_rows();
}

if($affectedRows1 > 0 && !empty($optimize['adapterbox_log'])) {
  $app->DB->Query('OPTIMIZE TABLE `adapterbox_log`');
  unset($optimize['adapterbox_log']);
}

$tage = (int)$app->erp->Firmendaten('cleaner_protokoll_tage');
if($tage > 15 || $tage <= 0) {
  $tage = 15;
}
$app->DB->Delete(
  sprintf(
    "DELETE FROM `cronjob_starter_running` 
    WHERE DATE_SUB(CURDATE(), INTERVAL %d DAY) >= `last_time` 
      AND `active` IN (0, -1)",
    $tage
  )
);
$affectedRows = (int)$app->DB->affected_rows();
if($affectedRows > 0 && !empty($optimize['cronjob_starter_running'])) {
  $app->DB->Query('OPTIMIZE TABLE `cronjob_starter_running`');
  unset($optimize['cronjob_starter_running']);
}
elseif($affectedRows > 50000) {
  $app->DB->Query('OPTIMIZE TABLE `cronjob_starter_running`');
}
$app->DB->Update(
  "UPDATE `prozessstarter` 
  SET `letzteausfuerhung` = NOW(), `mutex` = 1, `mutexcounter` = 0 
  WHERE `parameter` = 'cleaner' AND `aktiv` = 1"
);

if(!$app->DB->Select("SELECT `id` FROM `prozessstarter` WHERE `parameter` = 'cleaner' AND `aktiv` = 1")) {
  return;
}

$tage = (int)$app->erp->Firmendaten('cleaner_protokoll_tage');

if($tage > 15 || $tage <= 0) {
  $tage = 15;
}

$app->DB->Delete(
  sprintf(
    "DELETE FROM `cronjob_log` 
    WHERE DATE_SUB(CURDATE(), INTERVAL %d DAY) >= `change_time`",
    $tage
  )
);
$affectedRows = $app->DB->affected_rows();

if($affectedRows > 0 && !empty($optimize['cronjob_log'])) {
  $app->DB->Query('OPTIMIZE TABLE `cronjob_log`');
  unset($optimize['cronjob_log']);
}
elseif($affectedRows > 50000) {
  $app->DB->Query('OPTIMIZE TABLE `cronjob_log`');
}
$app->DB->Update(
  "UPDATE `prozessstarter` 
  SET `letzteausfuerhung` = NOW(), `mutex` = 1, `mutexcounter` = 0 
  WHERE `parameter` = 'cleaner' AND `aktiv` = 1"
);

if(!$app->DB->Select("SELECT `id` FROM `prozessstarter` WHERE `parameter` = 'cleaner' AND `aktiv` = 1")) {
  return;
}
if(method_exists($app->erp, 'cleanSpoolerAndFixNumbers')) {
  $app->erp->cleanSpoolerAndFixNumbers();
  $app->DB->Update(
    "UPDATE `prozessstarter` 
    SET `letzteausfuerhung` = NOW(), `mutex` = 1, `mutexcounter` = 0 
    WHERE `parameter` = 'cleaner' AND `aktiv` = 1"
  );
}

$app->DB->Delete(
  "DELETE `lr` 
  FROM `lager_reserviert` AS `lr` 
  INNER JOIN `auftrag` AS `o` ON lr.parameter = o.id AND lr.objekt = 'auftrag' 
  WHERE o.status = 'storniert'"
);

if(!empty($optimize)) {
  foreach($optimize as $table => $val) {
    if(!empty($tableSchemaByTables[$table])
      && $tableSchemaByTables[$table]['free'] > 0) {
      $app->DB->Query(sprintf("OPTIMIZE TABLE `%s`", $table));
    }
  }
}

$free = !function_exists('disk_free_space')?false:disk_free_space(dirname(__DIR__));
if($free !== false) {
  $diskFree = (int)floor($free / 1024);
  if(!empty($diskFreeId)){
    $app->DB->Update(
      sprintf(
        'UPDATE `system_disk_free` SET `disk_free_kb_end` = %d WHERE `id` = %d',
        $diskFree, $diskFreeId
      )
    );
  }
}
if($diskFreeId > 0) {
  $dbLength = (int)$app->DB->Select(
    sprintf(
      "SELECT ROUND(SUM(IFNULL(data_length,0) + IFNULL(index_length,0)) / 1024 / 1024)  
      FROM information_schema.TABLES
      WHERE `table_schema` = '%s'",
      $app->Conf->WFdbname
    )
  );
  if($dbLength > 0) {
    $app->DB->Update(
      sprintf(
        'UPDATE `system_disk_free` SET `db_size` = %d WHERE `id` = %d',
        $dbLength, $diskFreeId
      )
    );
  }

  $userdata = 0;
  try {
    if(function_exists('exec')){
      $userdata = @exec(sprintf('cd %s && du -c -d0 2>/dev/null', $app->Conf->WFuserdata), $out, $return);
      if(!empty($userdata)){
        $pos = strpos($userdata, 'total');
        if($pos === false){
          $pos = strpos($userdata, 'insgesamt');
        }
        if($pos === false && !empty($out)){
          $userdata = trim(reset($out));
          if(is_numeric($userdata)){
            $pos = strlen($userdata);
          }
        }
        if($pos > 0){
          $userdata = (int)(trim(substr($userdata, 0, $pos)) / 1024);
          if($userdata > 0){
            $app->DB->Update(
              sprintf(
                'UPDATE `system_disk_free` SET `userdata_mb_size` = %d WHERE id = %d',
                $userdata, $diskFreeId
              )
            );
          }
        }
        else{
          $userdata = 0;
        }
      }
      else{
        $userdata = 0;
      }
    }
  }
  catch(Exception $e) {
    $userdata = 0;
  }

  $app->erp->SetKonfigurationValue('userdatasize', $userdata);
}

$app->DB->Update(
  "UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 0,mutexcounter=0 WHERE parameter = 'cleaner'"
);

