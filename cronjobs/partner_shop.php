<?php
/*
include_once(dirname(__FILE__)."/../conf/main.conf.php");
include_once(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include_once(dirname(__FILE__)."/../www/lib/imap.inc.php");
include_once(dirname(__FILE__)."/../www/lib/class.erpapi.php");
if(file_exists(dirname(__FILE__)."/../www/lib/class.erpapi_custom.php"))include_once(dirname(__FILE__)."/../www/lib/class.erpapi_custom.php");
include_once(dirname(__FILE__)."/../www/lib/class.remote.php");
include_once(dirname(__FILE__)."/../www/lib/class.httpclient.php");
include_once(dirname(__FILE__)."/../www/lib/class.aes.php");
include_once(dirname(__FILE__)."/../www/plugins/phpmailer/class.phpmailer.php");
include_once(dirname(__FILE__)."/../www/plugins/phpmailer/class.smtp.php");
*/

if(!class_exists('app_t'))
{
  class app_t {
    var $DB;
    var $erp;
    var $user;
    var $remote;
  }
}

//ENDE
if(!isset($app))
{
  $app = new app_t();

  $conf = new Config();
  $app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass,null,$conf->WFdbport);
  $app->remote = new Remote($app);
  if(class_exists('erpAPICustom'))
  {
    $erp = new erpAPICustom($app);
  }else{
    $erp = new erpAPI($app);
  }
  $app->erp = $erp;
}

//$app->DB->Update("UPDATE artikel SET cache_lagerplatzinhaltmenge='0'");

$shops = $app->DB->SelectArr("SELECT id from shopexport s where s.geloescht <> '1' AND s.aktiv = '1'");
if($shops)
{
  foreach($shops as $shop)
  {
    $partner = $app->DB->SelectArr("SELECT 
    p.*,a.name,a.strasse,a.telefon,a.plz,a.ort,a.land,a.telefax,a.email from partner p left join adresse a on p.adresse = a.id  
    where p.shop= '".$shop['id']."' and p.geloescht <> '1'");
    if($partner)
    {
      $app->remote->RemoteSendPartner($shop['id'],$partner);
    }
  }
} else {
  echo "Keine Shops";
}
echo "\r\n";
/*
if($message !="")
{
  $erp->MailSend($erp->GetFirmaMail(),$erp->GetFirmaName(),$erp->GetFirmaBCC1(),"Lagerverwaltung","Systemmeldung: Auto Update Lagerlampen",$message);
}
*/

?>
