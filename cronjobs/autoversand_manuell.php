<?php
/*
include(dirname(__FILE__)."/../conf/main.conf.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include(dirname(__FILE__)."/../www/lib/imap.inc.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi.php");

class app_t {
  var $DB;
  var $user;
}
*/
// ende debug

/*
$debugfile = "/var/www/html/Xenomporio/debug.txt";

function file_append($filename,$text) {
  $oldtext = file_get_contents($filename);
  file_put_contents($filename,$oldtext.$text);
}

file_put_contents($debugfile,"1");
*/

if(empty($app)){
  $app = new app_t();
}
$DEBUG = 0;

if(empty($app->Conf)){
  $conf = new Config();
  $app->Conf = $conf;
}
if(empty($app->DB) || empty($app->DB->connection)){
  $app->DB = new DB($app->Conf->WFdbhost, $app->Conf->WFdbname, $app->Conf->WFdbuser, $app->Conf->WFdbpass, null, $app->Conf->WFdbport);
}
if(empty($app->erp)){
  if(class_exists('erpAPICustom')){
    $erp = new erpAPICustom($app);
  }else{
    $erp = new erpAPI($app);
  }
  $app->erp = $erp;
}
$app->String         = new WawiString();
if(class_exists('RemoteCustom'))
{
  $app->remote = new RemoteCustom($app);
}else{
  $app->remote = new Remote($app);
}
$app->Secure = new Secure($app);
$app->User = new User($app);
if(!defined('FPDF_FONTPATH'))
{
  define('FPDF_FONTPATH',dirname(__DIR__) . '/www/lib/pdf/font/');
}

$cronjobname = 'autoversand_manuell';

// START APPLICATION

$objAuftrag = $app->loadModule('auftrag');
if($objAuftrag == null || !method_exists($objAuftrag, 'AuftragVersand')) {
    $app->erp->LogFile($cronjobname." failed. Error while loading module 'auftrag'.");
    exit;
}

$pendingorders = $app->DB->SelectArr(
        "SELECT id 
        FROM auftrag AS a 
        WHERE  a.id!='' AND (a.belegnr!=0 OR a.belegnr!='') 
          AND a.status='freigegeben' AND a.autoversand='1' AND a.cronjobkommissionierung > 0
          AND a.inbearbeitung=0 AND a.nachlieferung!='1' AND a.vorkasse_ok='1' 
          AND a.porto_ok='1' AND a.lager_ok='1' AND a.check_ok='1' AND a.ust_ok='1' 
          AND a.liefertermin_ok='1' AND kreditlimit_ok='1' AND liefersperre_ok='1' 
        "
      );

if (!is_null($pendingorders)) {

  $processed_orders_num = 0;
  foreach ($pendingorders as $pendingorder) {
    /* Process each order */

    if($objAuftrag->AuftragVersand($pendingorder['id'])) {
      $processed_orders_num++;
    } else {
    }
    // Limit to 10 per call
    if ($processed_orders_num > 10) {
      break;
    }
  }
}

// END APPLICATION

$app->DB->Update("UPDATE prozessstarter SET mutex = 0 , mutexcounter = 0, letzteausfuerhung = now() WHERE (parameter = '".$cronjobname."' ) AND aktiv = 1");
