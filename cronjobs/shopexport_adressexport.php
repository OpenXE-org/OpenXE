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

include_once dirname(__DIR__) . '/www/lib/ShopimporterBase.php';

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

$conf = new Config();
$app->Conf = $conf;
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass, $app, $conf->WFdbport);
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

$app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter + 1 WHERE mutex = 1 AND (parameter = 'shopexport_adressexport') AND aktiv = 1");
if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE mutex = 0 AND parameter = 'shopexport_adressexport' AND aktiv = 1")){
  return;
}

$check = $app->DB->Select("SELECT COUNT(id) FROM shopexport_adressenuebertragen");
$app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 1,mutexcounter=0 WHERE parameter = 'shopexport_adressexport'");
while($check > 0)
{
  $shopadressen = $app->DB->Query("SELECT id,shop,adresse FROM shopexport_adressenuebertragen ORDER by id LIMIT 10");
  $anz = 0;
  while($row = $app->DB->Fetch_Array($shopadressen))
  {
    $anz++;
    try {
      (int)$app->remote->RemoteCommand($row['shop'],'sendadresse',$row['adresse']);
    }catch(Execption $exception)
    {
      $app->erp->LogFile($app->DB->real_escape_string($exception->getMessage()));
    }
    $app->DB->Delete("DELETE FROM shopexport_adressenuebertragen WHERE id='".$row['id']."' LIMIT 1");
    $app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 1,mutexcounter=0 WHERE parameter = 'shopexport_adressexport'");
  }
  //sleep(20); // das performance nicht total spinnt
  $check = $app->DB->Select("SELECT COUNT(id) FROM shopexport_adressenuebertragen");
}

$app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 0,mutexcounter=0 WHERE parameter = 'shopexport_adressexport'");
$app->erp->LogFile('Cronjob Adressuebertragung Ende '.$anz.' uebertragen');
