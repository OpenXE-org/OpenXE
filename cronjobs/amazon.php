<?php
use Xentral\Core\LegacyConfig\ConfigLoader;
include_once(dirname(__DIR__)."/conf/main.conf.php");
include_once(dirname(__DIR__)."/phpwf/plugins/class.mysql.php");
include_once(dirname(__DIR__)."/www/lib/imap.inc.php");
include_once(dirname(__DIR__)."/www/lib/class.erpapi.php");
if(file_exists(dirname(__DIR__)."/www/lib/class.erpapi_custom.php"))include_once(dirname(__DIR__)."/www/lib/class.erpapi_custom.php");
include_once(dirname(__DIR__)."/www/lib/class.remote.php");
if(file_exists(dirname(__DIR__)."/www/lib/class.remote_custom.php"))include_once(dirname(__DIR__)."/www/lib/class.remote_custom.php");
include_once(dirname(__DIR__)."/www/lib/class.httpclient.php");
if(!class_exists('AES')){
  $aes = '';
  $phpversion = (String)phpversion();
  if($phpversion{0} == '7' && (int)$phpversion{2} > 0) $aes = '2';
  if($aes == 2 && is_file(dirname(__DIR__) . "/www/lib/class.aes" . $aes . ".php")){
    include_once(dirname(__DIR__) . "/www/lib/class.aes" . $aes . ".php");
  }else
    include_once(dirname(__DIR__) . "/www/lib/class.aes.php");
}
include_once(dirname(__DIR__)."/www/pages/shopimport.php");
include_once(dirname(__DIR__)."/www/plugins/phpmailer/class.phpmailer.php");
include_once(dirname(__DIR__)."/www/plugins/phpmailer/class.smtp.php");
if(!class_exists('Secure')){
  include_once(dirname(__DIR__) . "/phpwf/plugins/class.secure.php");
}
include_once(dirname(__DIR__)."/phpwf/plugins/class.string.php");
include_once(dirname(__DIR__)."/phpwf/plugins/class.formhandler.php");
if(!class_exists('image')){
  include_once (dirname(__DIR__)."/www/lib/class.image.php");
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
include_once(dirname(__DIR__)."/www/lib/pdf/fpdf_final.php");
$classes = array('briefpapier','auftrag','angebot','rechnung','gutschrift');
foreach($classes as $class)
{
  if(file_exists(dirname(__DIR__)."/www/lib/dokumente/class.".$class."_custom.php"))
  {
    include_once(dirname(__DIR__)."/www/lib/dokumente/class.".$class."_custom.php");
  }elseif(file_exists(dirname(__DIR__)."/www/lib/dokumente/class.".$class.".php"))
  {
    include_once(dirname(__DIR__)."/www/lib/dokumente/class.".$class.".php");
  }
}
include_once(dirname(__DIR__)."/phpwf/plugins/class.string.php");
//ENDE
if(!class_exists('User'))
{
  class User
  {
    var $app;
    function __construct($app)
    {
      $this->app = $app;
    }
    
    function GetName()
    {
      return 'Cronjob';
    }
    
    function GetID()
    {
      return 0;
    }
    
    function GetFirma()
    {
      return 1;
    }
    
    function GetType()
    {
      return 'admin';
    }
    
    function DefaultProjekt()
    {
      return $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='1' LIMIT 1");
    }
    
    function GetAdresse()
    {
      return 0;
    }
    
    function GetUsername()
    {
      return 'Cronjob';
    }
    
  }
  
}

if(empty($app)) {
  $app = new app_t();
}
$app->User = new User($app);
if(empty($app->Conf)) {
  $conf = ConfigLoader::load();
  $app->Conf = $conf;
}
if(empty($app->DB) || empty($app->DB->connection)) {
  $app->DB = new DB($app->Conf->WFdbhost, $app->Conf->WFdbname, $app->Conf->WFdbuser, $app->Conf->WFdbpass, $app, $app->Conf->WFdbport);
}
if(empty($app->erp)) {
  if(class_exists('erpAPICustom')) {
    $erp = new erpAPICustom($app);
  }
  else {
    $erp = new erpAPI($app);
  }
  $app->erp = $erp;
}
$app->String = new WawiString();
$app->FormHandler = new FormHandler($app);
if(empty($app->remote)) {
  if(class_exists('RemoteCustom')) {
    $remote = new RemoteCustom($app);
  }
  else {
    $remote = new Remote($app);
  }
  $app->remote = $remote;
}
$app->Secure = new Secure($app);


$firmendatenid = $app->DB->Select("SELECT MAX(id) FROM firmendaten LIMIT 1");
$benutzername = $app->erp->Firmendaten('benutzername');
$passwort = $app->erp->Firmendaten('passwort');
$host = $app->erp->Firmendaten('host');
$port = $app->erp->Firmendaten('port');
$mailssl = $app->erp->Firmendaten('mailssl');
$mailanstellesmtp = $app->erp->Firmendaten('mailanstellesmtp');
$noauth = $app->erp->Firmendaten('noauth');


// mail
$app->mail = new PHPMailer($app);
$app->mail->CharSet = 'UTF-8';
$app->mail->PluginDir='plugins/phpmailer/';

if($mailanstellesmtp=='1'){
  $app->mail->IsMail();
} else {
  $app->mail->IsSMTP();

  if($noauth=='1') {
    $app->mail->SMTPAuth = false;
  }
  else {
    $app->mail->SMTPAuth   = true;
  }

  if($mailssl==1){
    $app->mail->SMTPSecure = 'tls';                 // sets the prefix to the servier
  }
  else if ($mailssl==2){
    $app->mail->SMTPSecure = 'ssl';                 // sets the prefix to the servier
  }

  $app->mail->Host       = $host;

  $app->mail->Port       = $port;                   // set the SMTP port for the GMAIL server

  $app->mail->Username   = $benutzername;  // GMAIL username
  $app->mail->Password   = $passwort;            // GMAIL password
}

$cronjob = $app->DB->Select("SELECT id FROM prozessstarter WHERE aktiv = 1 AND parameter = 'amazon' LIMIT 1");
if(!$cronjob) {
  return;
}

$app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung = now(), mutex = 0, mutexcounter = 0 WHERE id = '".$cronjob."' AND mutexcounter > 2 LIMIT 1");
if($app->DB->Select('SELECT id FROM prozessstarter WHERE mutex = 1 AND aktiv = 1 AND parameter = \'amazon\' LIMIT 1')) {
  $app->DB->Update(
    "UPDATE prozessstarter 
      SET letzteausfuerhung = now(), mutexcounter = mutexcounter + 1 
      WHERE aktiv = 1 AND parameter = 'amazon' AND mutex = 1 
      LIMIT 1"
  );
  return;
}
$app->erp->LogFile('Starte Amazon Cronjob');
$app->DB->Update(
  "UPDATE prozessstarter SET letzteausfuerhung = now(), mutex = 1, mutexcounter = 0 WHERE aktiv = 1 AND parameter = 'amazon'"
);
$shops = $app->DB->SelectArr(
    "SELECT id, bezeichnung,demomodus 
    FROM shopexport 
    WHERE aktiv = 1 AND geloescht <> 1 and (bezeichnung like '%amazon%'
    OR ((shoptyp = 'intern' OR shoptyp = 'custom') AND modulename = 'shopimporter_amazon'))"
);
if(!empty($shops)) {
  foreach($shops as $shop) {
    if(method_exists($app->erp, 'canRunCronjob') && !$app->erp->canRunCronjob(['amazon'])) {
      return;
    }
    $app->erp->ProzessstarterStatus('Schnittstelle: '.$shop['bezeichnung'], $cronjob);
    $app->remote->RemoteCommand($shop['id'], 'cronjob');
    $app->DB->Update(
      "UPDATE prozessstarter SET letzteausfuerhung = now(), mutex = 1, mutexcounter = 0 WHERE aktiv = 1 AND parameter = 'amazon'"
    );
    $app->erp->RunHook('amazon_cronjob', 1, $shop['id']);
    if(method_exists($app->erp, 'canRunCronjob') && !$app->erp->canRunCronjob(['amazon'])) {
      return;
    }
    $app->DB->Update(
      "UPDATE prozessstarter SET letzteausfuerhung = now(), mutex = 1, mutexcounter = 0 WHERE aktiv = 1 AND parameter = 'amazon'"
    );
  }
}
$app->DB->Update(
  "UPDATE prozessstarter SET letzteausfuerhung = now(), mutex = 0, mutexcounter = 0 WHERE aktiv = 1 AND parameter = 'amazon'"
);

$app->erp->LogFile('Ende Amazon Cronjob');

