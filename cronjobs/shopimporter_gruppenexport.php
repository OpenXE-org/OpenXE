<?php
/*
include_once(dirname(__FILE__)."/../conf/main.conf.php");
include_once(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include_once(dirname(__FILE__)."/../www/lib/imap.inc.php");
include_once(dirname(__FILE__)."/../www/lib/class.erpapi.php");
if(file_exists(dirname(__FILE__)."/../www/lib/class.erpapi_custom.php"))include_once(dirname(__FILE__)."/../www/lib/class.erpapi_custom.php");
include_once(dirname(__FILE__)."/../www/lib/class.remote.php");
include_once(dirname(__FILE__)."/../www/lib/class.httpclient.php");
include_once(dirname(__FILE__)."/../www/lib/class.aes.php");
include_once(dirname(__DIR__)."/phpwf/plugins/class.secure.php");
include_once(dirname(__FILE__)."/../www/plugins/phpmailer/class.phpmailer.php");
include_once(dirname(__FILE__)."/../www/plugins/phpmailer/class.smtp.php");
include_once(dirname(__DIR__)."/www/pages/shopimport.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.stringcleaner.php");
*/
if(!class_exists('Conf')){
  include_once dirname(__DIR__) . '/conf/main.conf.php';
}
if(!class_exists('DB')){
  include_once dirname(__DIR__) . '/phpwf/plugins/class.mysql.php';
}
if(!class_exists('IMAP')){
  include_once dirname(__DIR__) . '/www/lib/imap.inc.php';
}

if(!class_exists('erpAPI'))
{
  include_once dirname(__DIR__) . '/www/lib/class.erpapi.php';
}
if(file_exists(dirname(__DIR__).'/www/lib/class.erpapi_custom.php') &&
  !class_exists('erpAPICustom')){
  include_once dirname(__DIR__) . '/www/lib/class.erpapi_custom.php';
}
if(!class_exists('Remote')){
  include_once dirname(__DIR__) . '/www/lib/class.remote.php';
}
if(!class_exists('RemoteCustom') &&
  file_exists(dirname(__DIR__).'/www/lib/class.remote_custom.php')){
  include_once dirname(__DIR__) . '/www/lib/class.remote_custom.php';
}
if(!class_exists('HttpClient')){
  include_once dirname(__DIR__) . '/www/lib/class.httpclient.php';
}
if(!class_exists('AES')){
  $aes = '';
  $phpversion = PHP_VERSION;
  if(strpos($phpversion,'7') === 0 && (int)$phpversion{2} > 0)
  {
    $aes = '2';
  }
  if($aes === '2' && is_file(dirname(__DIR__) . '/www/lib/class.aes' . $aes . '.php')){
    include_once dirname(__DIR__) . '/www/lib/class.aes' . $aes . '.php';
  }else{
    include_once dirname(__DIR__) . '/www/lib/class.aes.php';
  }
}
if(!class_exists('Shopimport')){
  include_once dirname(__DIR__) . '/www/pages/shopimport.php';
}
if(!class_exists('ShopimportCustom') &&
  file_exists(dirname(__DIR__) . '/www/pages/shopimport_custom.php'))
{
  include_once dirname(__DIR__) . '/www/pages/shopimport_custom.php';
}
if(!class_exists('PHPMailer')){
  include_once dirname(__DIR__) . '/www/plugins/phpmailer/class.phpmailer.php';
}
if(!class_exists('SMTP')){
  include_once dirname(__DIR__) . '/www/plugins/phpmailer/class.smtp.php';
}
if(!class_exists('Secure')){
  include_once dirname(__DIR__) . '/phpwf/plugins/class.secure.php';
}
if(!class_exists('StringCleaner') && file_exists(dirname(__DIR__) . '/phpwf/plugins/class.stringcleaner.php'))
{
  include_once dirname(__DIR__) . '/phpwf/plugins/class.stringcleaner.php';
}
if(!class_exists('FormHandler')){
  include_once dirname(__DIR__) . '/phpwf/plugins/class.formhandler.php';
}
if(!class_exists('image')){
  include_once dirname(__DIR__) . '/www/lib/class.image.php';
}

if(!class_exists('FPDFWAWISION')){
  if(file_exists(dirname(__DIR__).'/conf/user_defined.php')){
    include_once dirname(__DIR__) . '/conf/user_defined.php';
  }
  if(defined('USEFPDF3') && USEFPDF3 && file_exists(dirname(__DIR__) . '/www/lib/pdf/fpdf_3.php')){
    require_once dirname(__DIR__) . '/www/lib/pdf/fpdf_3.php';
  }else if(defined('USEFPDF2') && USEFPDF2 && file_exists(dirname(__DIR__) . '/www/lib/pdf/fpdf_2.php')){
    require_once dirname(__DIR__) . '/www/lib/pdf/fpdf_2.php';
  }else{
    require_once dirname(__DIR__) . '/www/lib/pdf/fpdf.php';
  }
}
if(!class_exists('PDF_EPS')){
  include_once dirname(__DIR__) . '/www/lib/pdf/fpdf_final.php';
}
if(!class_exists('SuperFPDF')){
  include_once dirname(__DIR__) . '/www/lib/dokumente/class.superfpdf.php';
}
if(!class_exists('WawiString')){
  include_once dirname(__DIR__) . '/phpwf/plugins/class.string.php';
}

if(!defined('FPDF_FONTPATH'))
{
  define('FPDF_FONTPATH',dirname(__DIR__).'/www/lib/pdf/font/');
}

if(!class_exists('app_t'))
{
  class app_t {
    var $DB;
    var $erp;
    var $user;
    var $remote;
  }
}
//ENDE

if(!isset($app))
{
  $app = new app_t();
}
if(empty($app->Conf)){
  $conf = new Config();
  $app->Conf = $conf;
}
if(empty($app->DB) || empty($app->DB->connection)){
  $app->DB = new DB($app->Conf->WFdbhost, $app->Conf->WFdbname, $app->Conf->WFdbuser, $app->Conf->WFdbpass, $app, $app->Conf->WFdbport);
}
if(class_exists('erpAPICustom'))
{
  $erp = new erpAPICustom($app);
}else{
  $erp = new erpAPI($app);
}
$app->erp = $erp;
$app->String = new WawiString();
if(class_exists('RemoteCustom'))
{
  $remote = new RemoteCustom($app);
}else{
  $remote = new Remote($app);
}

$app->remote = $remote;;

$app->FormHandler = new FormHandler($app);

$app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter + 1 WHERE mutex = 1 AND (parameter = 'shopimporter_gruppenexport') AND aktiv = 1");
if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE mutex = 0 AND parameter = 'shopimporter_gruppenexport' AND aktiv = 1")){
  return;
}
$shops = $app->DB->SelectArr("SELECT * FROM shopexport_adressexport WHERE status = 'aktiv'");
if(count($shops)>0){
  foreach ($shops as $exportdataindex => $exportdata) {
    if($exportdata['status'] == 'aktiv'){
      $gruppen = $app->DB->SelectArr("SELECT * FROM gruppen WHERE art='preisgruppe' AND aktiv = 1");

      if(count($gruppen) > 0){
        foreach ($gruppen as $key => $gruppe) {
          $ret = (int)$app->remote->RemoteCommand($exportdata['shop_id'],'creategroup',$gruppe); 
          $app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE (parameter = 'shopimporter_gruppenexport' ) AND aktiv = 1");
        }
      }
    }
  }
}
$app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 0,mutexcounter=0,aktiv=0 WHERE parameter = 'shopimporter_gruppenexport'");
