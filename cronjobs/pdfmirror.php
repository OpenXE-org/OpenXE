<?php
set_time_limit(3600);


error_reporting(E_ERROR | E_WARNING | E_PARSE);

include_once(dirname(__DIR__)."/conf/main.conf.php");
include_once(dirname(__DIR__)."/phpwf/plugins/class.mysql.php");
include_once(dirname(__DIR__)."/phpwf/plugins/class.secure.php");
include_once(dirname(__DIR__)."/phpwf/plugins/class.user.php");
include_once(dirname(__DIR__)."/www/lib/imap.inc.php");
include_once(dirname(__DIR__)."/www/lib/class.erpapi.php");
include_once(dirname(__DIR__)."/www/lib/class.remote.php");
include_once(dirname(__DIR__)."/www/lib/class.httpclient.php");
$aes = '';
$phpversion = phpversion();
if($phpversion{0} == '7' && (int)$phpversion{2} > 0)$aes = '2';
if($aes == 2 && is_file(dirname(__DIR__)."/www/lib/class.aes".$aes.".php"))
{
  include_once(dirname(__DIR__)."/www/lib/class.aes".$aes.".php");
}else
  include_once(dirname(__DIR__)."/www/lib/class.aes.php");
include_once(dirname(__DIR__)."/www/plugins/phpmailer/class.phpmailer.php");
include_once(dirname(__DIR__)."/www/plugins/phpmailer/class.smtp.php");

if(!class_exists('app_t')){
  class app_t
  {
    var $DB;
    var $erp;
    var $User;
    var $mail;
    var $remote;
    var $Secure;
  }
}
//ENDE

if(!isset($app))
{

$app = new app_t();

$conf = new Config();
$app->Conf = $conf;
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass,null,$conf->WFdbport);
if(class_exists('erpAPICustom'))
{
  $erp = new erpAPICustom($app);
}else{
  $erp = new erpAPI($app);
}
$app->erp = $erp;


$app->Secure = new Secure($app);
$app->User = new User($app);
}
$app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter + 1 WHERE mutex = 1 AND parameter = 'pdfmirror'  AND aktiv = 1");
if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE mutex = 0 AND parameter = 'pdfmirror' AND aktiv = 1")){
  return;
}
$pdfmirror = $app->DB->Query("SELECT id from pdfmirror_md5pool WHERE pdfarchiv_id = 0 AND checksum <> ''");
if(!empty($pdfmirror))
{
  while($value = $app->DB->Fetch_Assoc($pdfmirror))
  {
    $newid = $app->erp->pdfmirrorZuArchiv($value['id']);
  }
  $app->DB->free($pdfmirror);
}
$app->DB->Update("UPDATE prozessstarter SET mutex = 0 , mutexcounter = 0 WHERE parameter = 'pdfmirror'  AND aktiv = 1");
