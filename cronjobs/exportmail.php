<?php
include_once("/home/eproo/eproo-master/app/main/conf/main.conf.php");
include_once("/home/eproo/eproo-master/app/main/phpwf/plugins/class.db.php");
include_once("/home/eproo/eproo-master/app/main/webroot/lib/imap.inc.php");
include_once("/home/eproo/eproo-master/app/main/webroot/lib/class.erpapi.php");
include_once("/home/eproo/eproo-master/app/main/webroot/plugins/phpmailer/class.phpmailer.php");
include_once("/home/eproo/eproo-master/app/main/webroot/plugins/phpmailer/class.smtp.php");



class app_t {
  var $DB;
  var $user;
}

$app = new app_t();

$DEBUG = 0;


$conf = new Config();
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass,null,$conf->WFdbport);
$erp = new erpAPI($app);

$firmendatenid = $app->DB->Select("SELECT MAX(id) FROM firmendaten LIMIT 1");

$benutzername = $app->DB->Select("SELECT benutzername FROM firmendaten WHERE id='".$firmendatenid."' LIMIT 1");
$passwort = $app->DB->Select("SELECT passwort FROM firmendaten WHERE id='".$firmendatenid."' LIMIT 1");
$host = $app->DB->Select("SELECT host FROM firmendaten WHERE id='".$firmendatenid."' LIMIT 1");
$port = $app->DB->Select("SELECT port FROM firmendaten WHERE id='".$firmendatenid."' LIMIT 1");
$mailssl = $app->DB->Select("SELECT mailssl FROM firmendaten WHERE id='".$firmendatenid."' LIMIT 1");
$noauth = $app->erp->Firmendaten("noauth");


$app->mail = new PHPMailer($app);
$app->mail->CharSet = "UTF-8";
//$app->mail->PluginDir="plugins/phpmailer/";
$app->mail->IsSMTP();

if($noauth=="1") $app->mail->SMTPAuth = false;
else $app->mail->SMTPAuth   = true;

if($mailssl)
$app->mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
$app->mail->Host       = $host;
$app->mail->Port       = $port;                   // set the SMTP port for the GMAIL server

$app->mail->Username   = $benutzername;
$app->mail->Password   = $passwort;



  $erp->ExportlinkZahlungsmail();

?>
