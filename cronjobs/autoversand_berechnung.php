<?php
use Xentral\Core\LegacyConfig\ConfigLoader;

include_once dirname(__DIR__).'/conf/main.conf.php';
include_once dirname(__DIR__).'/phpwf/plugins/class.mysql.php';
include_once dirname(__DIR__).'/www/lib/imap.inc.php';
include_once dirname(__DIR__).'/www/lib/class.erpapi.php';
if(file_exists(dirname(__DIR__).'/www/lib/class.erpapi_custom.php') &&
  !class_exists('erpAPICustom')){
  include_once dirname(__DIR__) . '/www/lib/class.erpapi_custom.php';
}

include_once dirname(__DIR__).'/www/plugins/phpmailer/class.phpmailer.php';
include_once dirname(__DIR__).'/www/plugins/phpmailer/class.smtp.php';

if(!class_exists('app_t')){
  class app_t extends ApplicationCore
  {
    var $DB;
    var $user;
    var $erp;
  }
}
if(empty($app) || !class_exists('ApplicationCore') || !($app instanceof ApplicationCore)) {
  $app = new app_t();
}
$app->User = new User($app);
if(empty($app->Conf)) {
  $conf = ConfigLoader::load();
  $app->Conf = $conf;
}
if(empty($app->DB) || empty($app->DB->connection)) {
  $app->DB = new DB($app->Conf->WFdbhost, $app->Conf->WFdbname, $app->Conf->WFdbuser, $app->Conf->WFdbpass, $app,$conf->WFdbport);
}

$DEBUG = 0;

//$erp = new erpAPI($app);
if(class_exists('erpAPICustom')) {
  $app->erp = new erpAPICustom($app);
}
else{
  $app->erp = new erpAPI($app);
}

$app->DB->Update(
  "UPDATE `prozessstarter` 
  SET `mutexcounter` = `mutexcounter` + 1 
  WHERE `mutex` = 1 AND `parameter` = 'autoversand_berechnung'  AND `aktiv` = 1"
);
if(
  !$app->DB->Select(
    "SELECT `id` FROM `prozessstarter` WHERE `mutex` = 0 AND `parameter` = 'autoversand_berechnung' AND `aktiv` = 1"
  )
) {
  return;
}

// Fuer den Autoversand gesperrte Auftraege mitberechnen
$autoVersandLockedOrders = $app->erp->Firmendaten('autoversand_locked_orders');
if($autoVersandLockedOrders == 1) {
  $auftraege = $app->DB->SelectFirstCols(
    "SELECT `id` 
    FROM `auftrag` 
    WHERE `status` = 'freigegeben' AND `inbearbeitung`= 0  
    ORDER BY `fastlane` = 1 DESC, `datum`, `id`"
  );
}
else{
  $auftraege = $app->DB->SelectFirstCols(
    "SELECT `id` 
    FROM `auftrag` 
    WHERE `status` = 'freigegeben' AND `inbearbeitung`= 0 AND `autoversand` = 1
    ORDER BY `fastlane` = 1 DESC, `datum`, `id`"
  );
}

if(empty($auftraege)) {
  return;
}
$cauftraege = count($auftraege);
echo 'Berechne '.$cauftraege." Auftraege\r\n";

foreach($auftraege as $key => $auftrag) {
  $app->erp->AuftragNeuberechnen($auftrag);
  $app->erp->AuftragEinzelnBerechnen($auftrag);
  if($key % 10 === 0) {
    if($key % 20 === 0
      && method_exists($app->erp, 'canRunCronjob') && !$app->erp->canRunCronjob(['autoversand_berechnung'])) {
      return;
    }
    $app->DB->Update(
      "UPDATE `prozessstarter` 
      SET `mutex` = 1 , `mutexcounter` = 0, `letzteausfuerhung` = NOW() 
      WHERE `parameter` = 'autoversand_berechnung'  AND `aktiv` = 1"
    );
  }
}
$app->DB->Update(
  "UPDATE `prozessstarter` 
  SET `mutex` = 0 , `mutexcounter` = 0, `letzteausfuerhung` = NOW() 
  WHERE `parameter` = 'autoversand_berechnung'  AND `aktiv` = 1"
);
