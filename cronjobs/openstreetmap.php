<?php
/*
include(dirname(__FILE__)."/../conf/main.conf.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include(dirname(__FILE__)."/../www/lib/imap.inc.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi.php");
include(dirname(__FILE__)."/../www/lib/class.remote.php");
include(dirname(__FILE__)."/../www/lib/class.httpclient.php");
include(dirname(__FILE__)."/../www/lib/class.aes.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.phpmailer.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.smtp.php");



class app_t {
  var $DB;
  var $erp;
  var $user;
  var $remote;
}
*/
//ENDE

if(file_exists(dirname(__DIR__).'/www/lib/class.erpapi_custom.php')) {
  include_once(dirname(__DIR__).'/www/lib/class.erpapi_custom.php');
}


if(empty($app))$app = new app_t();

if(empty($app->Conf)){
  $conf = new Config();
  $app->Conf = $conf;
}
if(empty($app->DB)) {
  $app->DB = new DB($app->Conf->WFdbhost,$app->Conf->WFdbname,$app->Conf->WFdbuser,$app->Conf->WFdbpass,null,$app->Conf->WFdbport);
}
if(class_exists('erpAPICustom'))
{
  $erp = new erpAPICustom($app);
}else{
  $erp = new erpAPI($app);
}
$app->erp = $erp;
$remote = new Remote($app);
$app->remote = $remote;

$apikey = $app->erp->GetKonfiguration('umkreissuche_einstellungen_apikey');
if($apikey==''){
  $apikey = $app->erp->GetKonfiguration('fahrtenbuch_einstellungen_apikey');
}
if($apikey == '') {
  return;
}
$app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter + 1 WHERE mutex = 1 AND parameter = 'openstreetmap'  AND aktiv = 1");
if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE mutex = 0 AND parameter = 'openstreetmap' AND aktiv = 1")){
  return;
}
$app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = NOW() WHERE parameter = 'openstreetmap'  AND aktiv = 1");

$adressen = $app->DB->SelectArr("SELECT a.id,a.plz, a.ort, a.land, a.strasse FROM adresse a LEFT JOIN openstreetmap_status os ON os.adresse=a.id WHERE (a.lat = 0 OR a.lat IS NULL) AND (a.lng = 0 OR a.lng IS NULL) AND a.geloescht!=1 AND a.ort!='' AND a.plz!='' AND (os.status IS NULL OR os.status<=0) ORDER by a.id  DESC");

if(empty($adressen)) {
  $app->DB->Delete('DELETE FROM openstreetmap_status');
} else{
  $cAdressen = count($adressen);
  $laender = $app->erp->GetSelectLaenderlisteEN();
  for ($ij = 0; $ij < $cAdressen; $ij++) {
    $land = $laender[$adressen[$ij]['land']];

    $query1 = trim($adressen[$ij]['strasse']) . ',' . trim($adressen[$ij]['plz']) . ' ' . trim($adressen[$ij]['ort']) . ',' . $land;
    $result1 = $app->erp->OpenstreetmapGetLangLat($apikey, $query1);
    if($result1[0] <> 0 && $result1[1] <> 0) {
      $app->DB->Update("UPDATE adresse SET lat='" . $result1[1] . "',lng='" . $result1[0] . "' WHERE id='" . $adressen[$ij]['id'] . "' LIMIT 1");
    }else{
      $app->DB->Delete("DELETE FROM openstreetmap_status WHERE adresse='" . $adressen[$ij]['id'] . "'");
      $app->DB->Insert("INSERT INTO openstreetmap_status (adresse,`status`) VALUES ('" . $adressen[$ij]['id'] . "','1')");
    }
    sleep(2);
  }
}
$app->DB->Update("UPDATE prozessstarter SET mutex = 0 , mutexcounter = 0, letzteausfuerhung = NOW() WHERE parameter = 'openstreetmap'  AND aktiv = 1");
