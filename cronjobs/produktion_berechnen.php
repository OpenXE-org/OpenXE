<?php
/*
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
  include_once dirname(__DIR__).'/www/lib/class.aes'.$aes.'.php';
}elseif(is_file(dirname(__DIR__) . '/www/lib/class.aes.php')){
  include_once dirname(__DIR__) . '/www/lib/class.aes.php';
}
include_once dirname(__DIR__).'/www/lib/class.remote.php';
include_once dirname(__DIR__).'/www/plugins/phpmailer/class.phpmailer.php';
include_once dirname(__DIR__).'/www/plugins/phpmailer/class.smtp.php';

class app_t extends ApplicationCore {
  public $DB;
  public $user;
  public $mail;
  public $erp;
  public $remote;
}
}
*/
//ENDE
if(empty($app)) {
  $app = new app_t();
}
if(empty($app->Conf)) {
  $conf = new Config();
  $app->Conf = $conf;
}
if(empty($app->DB) || empty($app->DB->connection)) {
  $app->DB = new DB($app->Conf->WFdbhost, $app->Conf->WFdbname, $app->Conf->WFdbuser, $app->Conf->WFdbpass, $app, $app->Conf->WFdbport);
}

$DEBUG = 0;


//$erp = new erpAPI($app);
if(class_exists('erpAPICustom')) {
  $app->erp = new erpAPICustom($app);
}
else{
  $app->erp = new erpAPI($app);
}

$app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter + 1 WHERE mutex = 1 AND parameter = 'produktion_berechnen'  AND aktiv = 1");
if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE mutex = 0 AND parameter = 'produktion_berechnen' AND aktiv = 1")) {
  return;
}

$query = sprintf("SELECT id FROM produktion WHERE status='%s' AND abgeschlossen=0 AND belegnr<>''", 'freigegeben');
$productionIds = $app->DB->SelectArr($query);

if(!empty($productionIds)){
  foreach ($productionIds as $key => $productionId){
    $app->erp->ProduktionEinzelnBerechnen($productionId['id']);
    $app->erp->ProduktionNeuberechnen($productionId['id']);

    if($key % 20 === 0) {
      $app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = NOW() WHERE parameter = 'produktion_berechnen'  AND aktiv = 1");
    }
  }
}
$app->DB->Update("UPDATE prozessstarter SET mutex = 0 , mutexcounter = 0, letzteausfuerhung = NOW() WHERE parameter = 'produktion_berechnen'  AND aktiv = 1");


