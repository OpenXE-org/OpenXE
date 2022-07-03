<?php
set_time_limit(3600);

/*
error_reporting(E_ERROR | E_WARNING | E_PARSE);

include(dirname(__FILE__)."/../conf/main.conf.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.secure.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.user.php");
include(dirname(__FILE__)."/../www/lib/imap.inc.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi.php");
include(dirname(__FILE__)."/../www/lib/class.remote.php");
include(dirname(__FILE__)."/../www/lib/class.httpclient.php");
include(dirname(__FILE__)."/../www/lib/class.aes.php");

class app_t {
  var $DB;
  var $erp;
  var $User;
  var $mail;
  var $remote;
  var $Secure;
}
*/
//ENDE

$app = new app_t();

$conf = new Config();
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass,null,$conf->WFdbport);
$erp = new erpAPI($app);
$app->erp = $erp;

$app->erp->LogFile("Folgebestaetigung gestartet");


$firmendatenid = $app->DB->Select("SELECT MAX(id) FROM firmendaten LIMIT 1");

$app->Secure = new Secure($app);
$app->User = new User($app);
$app->erp->LogFile("Folgebestaetigung start AuftraegeBerechnen");
$app->erp->AuftraegeBerechnen();
$app->erp->LogFile("Folgebestaetigung start Versand");

$result = $app->DB->SelectArr("SELECT DISTINCT adresse FROM auftrag WHERE status='freigegeben'");
for($i=0;$i<count($result);$i++)
{
  //echo "Adresse ".$result[$i]['adresse'];
  $app->erp->LogFile("Folgebestaetigung Adresse ".$result[$i]['adresse']);
  $app->erp->Folgebestaetigung($result[$i]['adresse']);
}

$app->erp->LogFile("Folgebestaetigung erfolgreich versendet");
//echo "done\r\n";

?>
