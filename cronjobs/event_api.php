<?php
include_once(dirname(__FILE__)."/../conf/main.conf.php");
include_once(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include_once(dirname(__FILE__)."/../www/lib/imap.inc.php");
include_once(dirname(__FILE__)."/../www/lib/class.erpapi.php");
include_once(dirname(__FILE__)."/../www/lib/class.remote.php");
include_once(dirname(__FILE__)."/../www/lib/class.httpclient.php");
$aes = '';
$phpversion = (String)phpversion();
if($phpversion[0] == '7' && (int)$phpversion[2] > 0)$aes = '2';
if($aes == 2 && is_file(dirname(__FILE__)."/../www/lib/class.aes".$aes.".php"))
{
  include_once(dirname(__FILE__)."/../www/lib/class.aes".$aes.".php");
}else
  include_once(dirname(__FILE__)."/../www/lib/class.aes.php");

if(!class_exists('app_t'))
{
  class app_t {
    var $DB;
    var $erp;
    var $user;
  }
}

//ENDE

error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);

$app = new app_t();

$conf = new Config();
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass,null,$conf->WFdbport);
$erp = new erpAPI($app);
$app->erp = $erp;

//$app->DB->Update("UPDATE artikel SET cache_lagerplatzinhaltmenge='0'");

$firmendatenid = $app->DB->Select("SELECT MAX(id) FROM firmendaten LIMIT 1");

$events = $app->DB->SelectArr("SELECT * FROM event_api");

for($ij=0;$ij<count($events);$ij++)
{
	$result = $app->erp->EventCall($events[$ij]['id'],true);

	if($result)
	{
		echo "SUCCESS :".$events[$ij]['eventname']." Parameter: ".$events[$ij]['parameter']."\r\n";
	} else {
		echo "ERROR   :".$events[$ij]['eventname']." Parameter: ".$events[$ij]['parameter']."\r\n";
	}
}

/*
if($message !="")
{
  $erp->MailSend($erp->GetFirmaMail(),$erp->GetFirmaName(),$erp->GetFirmaBCC1(),"Lagerverwaltung","Systemmeldung: Auto Update Lagerlampen",$message);
}
*/


?>
