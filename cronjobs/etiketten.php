<?php

set_time_limit(3600);


error_reporting(E_ERROR | E_WARNING | E_PARSE);

include_once(dirname(__FILE__)."/../conf/main.conf.php");
include_once(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include_once(dirname(__FILE__)."/../phpwf/plugins/class.secure.php");
include_once(dirname(__FILE__)."/../phpwf/plugins/class.user.php");
if(file_exists(dirname(__FILE__).'/../conf/user_defined.php'))include_once(dirname(__FILE__).'/../conf/user_defined.php');
if(defined('USEFPDF3') && USEFPDF3)
{
  if(file_exists(__DIR__ .'/../www/lib/pdf/fpdf_3.php'))
  {
    require_once(__DIR__ .'/../www/lib/pdf/fpdf_3.php');
  }else {
    require_once(__DIR__ .'/../www/lib/pdf/fpdf.php');
  }
}
else if(defined('USEFPDF2') && USEFPDF2)
{
  if(file_exists(__DIR__ .'/../www/lib/pdf/fpdf_2.php'))
  {
    require_once(__DIR__ .'/../www/lib/pdf/fpdf_2.php');
  }else {
    require_once(__DIR__ .'/../www/lib/pdf/fpdf.php');
  }
} else {
  require_once(__DIR__ .'/../www/lib/pdf/fpdf.php');
}
include_once(dirname(__FILE__)."/../www/lib/pdf/fpdf_final.php");
include_once(dirname(__FILE__)."/../www/lib/imap.inc.php");
include_once(dirname(__FILE__)."/../www/lib/class.erpapi.php");
include_once(dirname(__FILE__)."/../www/lib/class.remote.php");
include_once(dirname(__FILE__)."/../www/lib/class.httpclient.php");
$aes = '';
$phpversion = (String)phpversion();
if($phpversion{0} == '7' && (int)$phpversion{2} > 0)$aes = '2';
if($aes == 2 && is_file(dirname(__FILE__)."/../www/lib/class.aes".$aes.".php"))
{
  include_once(dirname(__FILE__)."/../www/lib/class.aes".$aes.".php");
}else
  include_once(dirname(__FILE__)."/../www/lib/class.aes.php");
include_once(dirname(__FILE__)."/../www/lib/class.printer.php");
include_once(dirname(__FILE__)."/../www/plugins/phpmailer/class.phpmailer.php");
include_once(dirname(__FILE__)."/../www/plugins/phpmailer/class.smtp.php");


include_once(dirname(__FILE__)."/../www/lib/dokumente/class.superfpdf.php");
include_once(dirname(__FILE__)."/../www/lib/dokumente/class.etiketten.php");

include_once(dirname(__FILE__)."/../phpwf/plugins/class.string.php");
class app_t {
  var $DB;
  var $erp;
  var $User;
  var $mail;
  var $remote;
  var $Secure;
}

//ENDE

if(!isset($app))
{

$app = new app_t();

$conf = new Config();
$app->Conf = $conf;
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass,null,$conf->WFdbport);
$erp = new erpAPI($app);
$app->erp = $erp;
$app->String         = new WawiString();
$app->erp->LogFile("MLM gestartet");
$app->printer = new Printer($app);

$app->Secure = new Secure($app);
$app->User = new User($app);
if(!defined('FPDF_FONTPATH'))define('FPDF_FONTPATH',dirname(__FILE__)."/../www/lib/pdf/font/");
}


//Allgemein

$etikettendrucker=7;
$menge = 1;

//Artikel 
/*
$result = $app->DB->SelectArr("SELECT id,nummer FROM artikel WHERE geloescht!=1 AND nummer!='' ORDER by nummer LIMIT 10");
foreach($result as $row)
{
  $app->erp->EtikettenDrucker("artikel_klein",$menge,"artikel",$row['id'],"","",$etikettendrucker,$row['nummer']);
}
*/

//Lager
$result = $app->DB->SelectArr("SELECT id,kurzbezeichnung FROM lager_platz LIMIT 10");
foreach($result as $row)
{
  $app->erp->EtikettenDrucker("lagerplatz_klein",$menge,"lager_platz",$row['id'],"","",$etikettendrucker,$row['kurzbezeichnung']);
}



?>
