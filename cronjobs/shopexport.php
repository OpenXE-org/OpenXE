<?php
include(dirname(__FILE__)."/../conf/main.conf.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include(dirname(__FILE__)."/../www/lib/imap.inc.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi.php");
include(dirname(__FILE__)."/../www/lib/class.remote.php");
include(dirname(__FILE__)."/../www/lib/class.httpclient.php");
$aes = '';
$phpversion = (String)phpversion();
if($phpversion{0} == '7' && (int)$phpversion{2} > 0)$aes = '2';
if($aes == 2 && is_file(dirname(__FILE__)."/../www/lib/class.aes".$aes.".php"))
{
  include_once(dirname(__FILE__)."/../www/lib/class.aes".$aes.".php");
}else
  include_once(dirname(__FILE__)."/../www/lib/class.aes.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.phpmailer.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.smtp.php");



class app_t {
  var $DB;
  var $user;
  var $erp;
}

//ENDE

echo "start 1 lagerzahl\r\n";
$app = new app_t();

echo "start  2lagerzahl\r\n";

$conf = new Config();
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass,null,$conf->WFdbport);
$erp = new erpAPI($app);
$app->erp = $erp;
$remote = new Remote($app);

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

$shop=5;

//$app->DB->Update("UPDATE artikel SET hersteller='OLIMEX' WHERE hersteller='Olimex Ltd.'");
//exit;
$shopartikel = $app->DB->SelectArr("SELECT id,name_de,nummer FROM artikel WHERE shop='$shop' OR shop2='$shop' OR shop3='$shop' AND geloescht!='1' AND inaktiv!='1'");
echo "Artikel gefunden: ".count($shopartikel)."\r\n";

for($ij=0;$ij<count($shopartikel);$ij++)
{
		echo "Artikel: ".$shopartikel[$ij]['name_de']." ".$shopartikel[$ij]['nummer']."\r\n";
    $shop1 = $app->DB->Select("SELECT shop FROM artikel WHERE id='".$shopartikel[$ij]['id']."' LIMIT 1");
		if($shop1>0 && $shop==$shop1)
  		$remote->RemoteSendArticleList($shop,array($shopartikel[$ij]['id']));
 
		$shop2 = $app->DB->Select("SELECT shop2 FROM artikel WHERE id='".$shopartikel[$ij]['id']."' LIMIT 1");
		if($shop2>0 && $shop==$shop2)
  		$remote->RemoteSendArticleList($shop2,array($shopartikel[$ij]['id']));

		$shop3 = $app->DB->Select("SELECT shop3 FROM artikel WHERE id='".$shopartikel[$ij]['id']."' LIMIT 1");
		if($shop3>0 && $shop==$shop3)
  		$remote->RemoteSendArticleList($shop3,array($shopartikel[$ij]['id']));
}

if($message !="")
{
//  $erp->MailSend($erp->GetFirmaMail(),$erp->GetFirmaName(),$erp->GetFirmaMail(),"Lagerverwaltung","Systemmeldung: Auto Update Lagerlampen",$message);
}


?>
