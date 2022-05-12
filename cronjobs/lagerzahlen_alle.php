<?php
/*
include(dirname(__FILE__)."/../conf/main.conf.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include(dirname(__FILE__)."/../www/lib/imap.inc.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi_custom.php");
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

$app = new app_t();

$conf = new Config();
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass,null,$conf->WFdbport);
$erp = new erpAPICustom($app);
$app->erp = $erp;


//$app->DB->Update("UPDATE artikel SET cache_lagerplatzinhaltmenge='0'");

$firmendatenid = $app->DB->Select("SELECT MAX(id) FROM firmendaten LIMIT 1");


$lagerartikel = $app->DB->SelectArr("SELECT id,restmenge,name_de,lieferzeit,cache_lagerplatzinhaltmenge,juststueckliste FROM artikel WHERE (lagerartikel='1' OR (stueckliste='1' AND juststueckliste='1'))");

echo "count ".count($lagerartikel);

for($ij=0;$ij<count($lagerartikel);$ij++)
{
	$app->erp->LagerSync($lagerartikel[$ij]['id'],true);
}
/*
if($message !="")
{
  $erp->MailSend($erp->GetFirmaMail(),$erp->GetFirmaName(),$erp->GetFirmaBCC1(),"Lagerverwaltung","Systemmeldung: Auto Update Lagerlampen",$message);
}
*/

?>
