<?php
/*
include(dirname(__FILE__)."/../conf/main.conf.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include(dirname(__FILE__)."/../www/lib/imap.inc.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi.php");
include(dirname(__FILE__)."/../www/lib/class.remote.php");
include(dirname(__FILE__)."/../www/lib/class.httpclient.php");
include(dirname(__FILE__)."/../www/lib/class.aes.php");

class app_t {
  var $DB;
  var $erp;
  var $user;
  var $remote;
}
*/
//ENDE

if(file_exists(dirname(__FILE__)."/../www/lib/class.erpapi_custom.php"))include_once(dirname(__FILE__)."/../www/lib/class.erpapi_custom.php");

if(empty($app))$app = new app_t();

if(empty($app->Conf)) {
  $conf = new Config();
  $app->Conf = $conf;
}
if(empty($app->DB) || empty($app->DB->connection)) {
  $app->DB = new DB($app->Conf->WFdbhost, $app->Conf->WFdbname, $app->Conf->WFdbuser, $app->Conf->WFdbpass, null, $app->Conf->WFdbport);
}
if(!isset($app->erp) || !$app->erp) {
  if (class_exists('erpAPICustom')) {
    $erp = new erpAPICustom($app);
  } else {
    $erp = new erpAPI($app);
  }
//$remote = new Remote($app);
  $app->erp = $erp;
//$app->remote= $remote;
}
if(empty($app->remote)) {
  if(is_file(dirname(__DIR__) . '/www/lib/class.remote_custom.php')) {
    if(!class_exists('RemoteCustom')){
      require_once dirname(__DIR__) . '/www/lib/class.remote_custom.php';
    }
    $app->remote = new RemoteCustom($app);
  }
  else {
    $app->remote = new Remote($app);
  }
}

$app->erp->LogFile("Starte Synchronisation");

//$app->DB->Update("UPDATE artikel SET cache_lagerplatzinhaltmenge='999'");

$firmendatenid = $app->DB->Select("SELECT MAX(id) FROM firmendaten LIMIT 1");

$obj = $app->erp->LoadModul('vertriebscockpit');
if($obj)
{
  $obj->CalcCache();
}
