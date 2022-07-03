<?php

if(!class_exists('erpAPICustom') && file_exists(dirname(__DIR__).'/www/lib/class.erpapi_custom.php')) {
  include_once(dirname(__DIR__).'/www/lib/class.erpapi_custom.php');
}

if(empty($app)) {
  $app = new app_t();
}
if(empty($app->Conf)){
  $conf = new Config();
  $app->Conf = $conf;
}
if(empty($app->DB)){
  $app->DB = new DB($app->Conf->WFdbhost,$app->Conf->WFdbname,$app->Conf->WFdbuser,$app->Conf->WFdbpass, $app, $app->Conf->WFdbport);
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

$app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter + 1 WHERE mutex = 1 AND parameter = 'bestellungabschliessen'  AND aktiv = 1");
if(!$app->erp->Firmendaten('bestellungabschliessen') || !$app->DB->Select("SELECT id FROM prozessstarter WHERE mutex = 0 AND parameter = 'bestellungabschliessen' AND aktiv = 1")){
  return;
}
$app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = NOW() WHERE parameter = 'bestellungabschliessen'  AND aktiv = 1");

$offene = $app->DB->Query("SELECT id FROM bestellung WHERE status='freigegeben' OR status='versendet'");
if(!empty($offene)) {
  /** @var Bestellung $obj */
  $obj = $app->erp->LoadModul('bestellung');
  if(!empty($obj)) {
    while($order = $app->DB->Fetch_Assoc($offene)){
      $obj->checkAbschliessen($order['id']);
    }
  }
  $app->DB->free($offene);
}
$app->DB->Update("UPDATE prozessstarter SET mutex = 0 , mutexcounter = 0 WHERE parameter = 'bestellungabschliessen'  AND aktiv = 1");
