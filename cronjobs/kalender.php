<?php
/*
include(dirname(__FILE__)."/../conf/main.conf.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include(dirname(__FILE__)."/../www/lib/imap.inc.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.phpmailer.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.smtp.php");



class app_t {
  var $DB;
  var $user;
  var $erp;
}

*/
//ENDE


$DEBUG = 0;


if(empty($app)){
  $app = new app_t();
}
if(empty($app->Conf)){
  $conf = new Config();
  $app->Conf = $conf;
}
if(empty($app->DB) || empty($app->DB->connection)){
  $app->DB = new DB($app->Conf->WFdbhost, $app->Conf->WFdbname, $app->Conf->WFdbuser, $app->Conf->WFdbpass, null, $app->Conf->WFdbport);
}
if(empty($app->erp)){
  if(class_exists('erpAPICustom')){
    $erp = new erpAPICustom($app);
  }else{
    $erp = new erpAPI($app);
  }
  $app->erp = $erp;
}

$firmendatenid = $app->DB->Select("SELECT MAX(id) FROM firmendaten LIMIT 1");

$benutzername = $app->erp->Firmendaten("benutzername");
$passwort = $app->erp->Firmendaten("passwort");
$host = $app->erp->Firmendaten("host");
$port = $app->erp->Firmendaten("port");
$mailssl = $app->erp->Firmendaten("mailssl");
$mailanstellesmtp = $app->erp->Firmendaten("mailanstellesmtp");
$noauth = $app->erp->Firmendaten("noauth");


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

$app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter + 1 WHERE mutex = 1 AND (parameter = 'kalender' ) AND aktiv = 1");
if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE mutex = 0 AND (parameter = 'kalender') AND aktiv = 1"))return;


// alle termine innerhalb der nächsten 15 Minuten
$termine = $app->DB->SelectArr("SELECT ke.id,ke.bezeichnung,DATE_FORMAT(ke.von,'%H:%i') as start,ke.beschreibung,ke.allDay 
    FROM kalender_event AS ke 
    WHERE ke.von <= DATE_ADD(NOW(),INTERVAL 15 MINUTE) AND ke.von >= NOW() AND ke.erinnerung='1'");
$ctermine = !empty($termine)?count($termine):0;
$firmenmail = $erp->GetFirmaMail();
$firmaname = $erp->GetFirmaName();
for($i=0;$i<$ctermine;$i++)
{
  $users = $app->DB->SelectArr("SELECT DISTINCT userid FROM kalender_user WHERE event='".$termine[$i]['id']."' AND userid > 0");
  $cusers = !empty($users)?count($users):0;
  for($u=0;$u<$cusers;$u++)
  {
    $userid = $users[$u]['userid'];
    $adressearr = $app->DB->SelectRow("SELECT a.email,a.name 
      FROM `user` AS u 
      INNER JOIN adresse AS a ON a.id=u.adresse 
      WHERE u.id='".$userid."' AND u.kalender_ausblenden=0 
      LIMIT 1");
    if(!empty($adressearr) && !empty($adressearr['email']))
    {
      $name = $adressearr['name'];
      $email = $adressearr['email'];
    }else{
      continue;
    }
    $app->erp->MailSend($firmenmail,$firmaname,$email,$name,'ERINNERUNG TERMIN: '.$termine[$i]['bezeichnung'].' (um '.$termine[$i]['start'].')',"Erinnerung von Xentral:\r\n\r\n".$termine[$i]['beschreibung'],'','',false);

    if($termine[$i]['allDay']){
      $app->erp->InternesEvent($userid, '<b>Termin:  ' . $termine[$i]['bezeichnung'] . '</b><br> Kalender-Erinnerung', 'alert', 1);
    }
    else{
      $app->erp->InternesEvent($userid, '<b>Termin:  ' . $termine[$i]['bezeichnung'] . ' (um ' . $termine[$i]['start'] . ')</b><br> Kalender-Erinnerung', 'alert', 1);
    }
  }
}

$app->DB->Update("UPDATE prozessstarter SET mutex = 0 , mutexcounter = 0 WHERE (parameter = 'kalender' ) AND aktiv = 1");
