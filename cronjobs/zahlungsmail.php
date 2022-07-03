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
if(!class_exists('WawiString')){
  include_once(dirname(__DIR__) . '/phpwf/plugins/class.string.php');
}
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

$app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter + 1 WHERE mutex = 1 AND (parameter = 'zahlungsmail' ) AND aktiv = 1");
if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE mutex = 0 AND (parameter = 'zahlungsmail') AND aktiv = 1")) {
  return;
}
$app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE (parameter = 'zahlungsmail' ) AND aktiv = 1");
$meineauftraege = $app->DB->SelectArr(
  "SELECT id FROM auftrag 
    WHERE status='freigegeben' AND vorkasse_ok!='1' AND zahlungsweise='vorkasse' AND 
          zahlungsweise!='nachnahme' AND IFNULL(zahlungsmailcounter,0) < 3"
);

if(!empty($meineauftraege)){
  foreach ($meineauftraege as $key => $auftrag) {
    $app->erp->AuftragZahlungsmail($auftrag['id']);
    if($key % 10 === 0) {
      $app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE (parameter = 'zahlungsmail' ) AND aktiv = 1");
    }
  }
}

$app->DB->Update("UPDATE prozessstarter SET mutex = 0 , mutexcounter = 0, letzteausfuerhung = now() WHERE (parameter = 'zahlungsmail' ) AND aktiv = 1");
