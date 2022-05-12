<?php
/*
include(dirname(__FILE__)."/../conf/main.conf.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.db.php");
include(dirname(__FILE__)."/../www/lib/imap.inc.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.phpmailer.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.smtp.php");




class app_t {
  var $DB;
  var $user;
}

//ENDE
*/


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
  $app->mail->CharSet = "UTF-8";
  //$app->mail->PluginDir="plugins/phpmailer/";

  if($mailanstellesmtp=="1"){
    $app->mail->IsMail();
  } else {
    $app->mail->IsSMTP();

    if($noauth=="1")
    {
      $app->mail->SMTPAuth = false;
    }
    else
    {
      $app->mail->SMTPAuth   = true;
    }

    if($mailssl==1){
      $app->mail->SMTPSecure = "tls";                 // sets the prefix to the servier
    }
    else if ($mailssl==2){
      $app->mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
    }

    $app->mail->Host       = $host;

    $app->mail->Port       = $port;                   // set the SMTP port for the GMAIL server

    $app->mail->Username   = $benutzername;  // GMAIL username
    $app->mail->Password   = $passwort;            // GMAIL password
  }


//$app->erp->LogFile("start aufgabe");

$app->DB->Update("UPDATE aufgabe SET email_gesendet_vorankuendigung=0 WHERE DATE_SUB(abgabe_bis, INTERVAL vorankuendigung DAY) = DATE_FORMAT(NOW(),'%Y-%m-%d') AND vorankuendigung > 0 AND DATE_FORMAT(NOW(),'%H:%i') < DATE_FORMAT(abgabe_bis_zeit,'%H:%i')");

//einmalig
$app->DB->Update("UPDATE aufgabe SET email_gesendet=0 WHERE DATE_FORMAT(abgabe_bis, '%Y-%m-%d') = DATE_FORMAT(NOW(),'%Y-%m-%d') AND DATE_FORMAT(NOW(),'%H:%i') < DATE_FORMAT(abgabe_bis_zeit,'%H:%i')");


// vorankündigung einmalig
$meineauftraege = $app->DB->SelectArr("SELECT id FROM aufgabe WHERE emailerinnerung='1' AND DATE_SUB(abgabe_bis, INTERVAL vorankuendigung DAY) = DATE_FORMAT(NOW(),'%Y-%m-%d') AND vorankuendigung > 0 AND DATE_FORMAT(NOW(),'%H:%i') >= DATE_FORMAT(abgabe_bis_zeit,'%H:%i') AND email_gesendet_vorankuendigung!=1 AND intervall_tage=0 AND status!='abgeschlossen'");
$cmeineauftraege = !empty($meineauftraege)?count($meineauftraege):0;
for($i=0;$i<$cmeineauftraege;$i++)
{
  //$this->AuftragEinzelnBerechnen($meineauftraege[$i][id]);
	//echo "Sende Aufgabe ID ".$meineauftraege[$i][id]."\r\n";
  $app->erp->AufgabenMail($meineauftraege[$i]['id'],true);
  $app->DB->Update("UPDATE aufgabe SET email_gesendet_vorankuendigung=1 WHERE id='".$meineauftraege[$i]['id']."' LIMIT 1");
}


// echte mail einmalig
$meineauftraege = $app->DB->SelectArr("SELECT id FROM aufgabe WHERE emailerinnerung='1' AND DATE_FORMAT(abgabe_bis, '%Y-%m-%d') = DATE_FORMAT(NOW(),'%Y-%m-%d') AND DATE_FORMAT(NOW(),'%H:%i') >= DATE_FORMAT(abgabe_bis_zeit,'%H:%i') AND email_gesendet!=1 AND intervall_tage=0 AND status!='abgeschlossen'");

//$app->erp->LogFile("start aufgabe 2 Anzahl: ".count($meineauftraege));
$cmeineauftraege = !empty($meineauftraege)?count($meineauftraege):0;
for($i=0;$i<$cmeineauftraege;$i++)
{
  //$this->AuftragEinzelnBerechnen($meineauftraege[$i][id]);
	//echo "Sende Aufgabe ID ".$meineauftraege[$i][id]."\r\n";
//  $app->erp->LogFile("starte aufgabe Aufgabe: ".$meineauftraege[$i][id]);
  $app->erp->AufgabenMail($meineauftraege[$i]['id']);
  //$app->erp->LogFile("gesendet Aufgabe: ".$meineauftraege[$i][id]);

  $app->DB->Update("UPDATE aufgabe SET email_gesendet=1 WHERE id='".$meineauftraege[$i]['id']."' LIMIT 1");
}


// echte mail taeglich
$app->DB->Update("UPDATE aufgabe SET email_gesendet=0 WHERE DATE_FORMAT(NOW(),'%H:%i') < DATE_FORMAT(abgabe_bis_zeit,'%H:%i') AND intervall_tage=1");
$meineauftraege = $app->DB->SelectArr("SELECT id FROM aufgabe WHERE emailerinnerung='1' AND DATE_FORMAT(NOW(),'%H:%i') >= DATE_FORMAT(abgabe_bis_zeit,'%H:%i') AND email_gesendet!=1 AND intervall_tage=1 AND status!='abgeschlossen'");
$cmeineauftraege = !empty($meineauftraege)?count($meineauftraege):0;
for($i=0;$i<$cmeineauftraege;$i++)
{
  //$this->AuftragEinzelnBerechnen($meineauftraege[$i][id]);
	//echo "Sende Aufgabe ID ".$meineauftraege[$i][id]."\r\n";
  $app->erp->AufgabenMail($meineauftraege[$i]['id']);
  $app->DB->Update("UPDATE aufgabe SET email_gesendet=1 WHERE id='".$meineauftraege[$i]['id']."' LIMIT 1");
}

// echte mail woechentlich
//TODO
$app->DB->Update("UPDATE aufgabe SET email_gesendet=0 WHERE DATE_FORMAT(NOW(),'%w') != DATE_FORMAT(abgabe_bis_zeit,'%w') AND intervall_tage=2");

$meineauftraege = $app->DB->SelectArr("SELECT id FROM aufgabe WHERE emailerinnerung='1' AND DATE_FORMAT(NOW(),'%w') = DATE_FORMAT(abgabe_bis_zeit,'%w') 
AND DATE_FORMAT(NOW(),'%H:%i') >= DATE_FORMAT(abgabe_bis_zeit,'%H:%i') AND email_gesendet!=1 AND intervall_tage=2 AND status!='abgeschlossen'");
$cmeineauftraege = !empty($meineauftraege)?count($meineauftraege):0;
for($i=0;$i<$cmeineauftraege;$i++)
{
  //$this->AuftragEinzelnBerechnen($meineauftraege[$i][id]);
	//echo "Sende Aufgabe ID ".$meineauftraege[$i][id]."\r\n";
  $app->erp->AufgabenMail($meineauftraege[$i]['id']);
  $app->DB->Update("UPDATE aufgabe SET email_gesendet=1 WHERE id='".$meineauftraege[$i]['id']."' LIMIT 1");
}


// echte mail monatlich
$app->DB->Update("UPDATE aufgabe SET email_gesendet=0 WHERE DATE_FORMAT(abgabe_bis, '%d') = DATE_FORMAT(NOW(),'%d') AND DATE_FORMAT(NOW(),'%H:%i') < DATE_FORMAT(abgabe_bis_zeit,'%H:%i') AND intervall_tage=3");
$meineauftraege = $app->DB->SelectArr("SELECT id FROM aufgabe WHERE emailerinnerung='1' AND DATE_FORMAT(abgabe_bis, '%d') = DATE_FORMAT(NOW(),'%d') AND DATE_FORMAT(NOW(),'%H:%i') >= DATE_FORMAT(abgabe_bis_zeit,'%H:%i') AND email_gesendet!=1 AND intervall_tage=3 AND status!='abgeschlossen'");
$cmeineauftraege = !empty($meineauftraege)?count($meineauftraege):0;
for($i=0;$i<$cmeineauftraege;$i++)
{
  //$this->AuftragEinzelnBerechnen($meineauftraege[$i][id]);
	//echo "Sende Aufgabe ID ".$meineauftraege[$i][id]."\r\n";
  $app->erp->AufgabenMail($meineauftraege[$i]['id']);
  $app->DB->Update("UPDATE aufgabe SET email_gesendet=1 WHERE id='".$meineauftraege[$i]['id']."' LIMIT 1");
}


//$app->erp->LogFile("ende aufgabe");

//TODO jaehrlich


