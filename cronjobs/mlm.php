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
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.phpmailer.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.smtp.php");

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

$app->erp->LogFile("MLM gestartet");

$app->Secure = new Secure($app);
$app->User = new User($app);

include_once(dirname(__FILE__) . "/../www/pages/multilevel.php");
$mlm = new Multilevel($app,true);
$result = $app->DB->SelectArr("SELECT id FROM adresse WHERE mlmaktiv=1");

$shop = $app->DB->SelectArr("SELECT id from shopexport s where s.geloescht <> '1' AND s.aktiv = '1' LIMIT 1");

for($i=0;$i<count($result);$i++)
{
  $partnercheck = $app->DB->Select("SELECT id FROM partner where adresse = '".$result[$i]['id']."'");
  
  if(!$partnercheck)
  {
    $name = $app->DB->Select("SELECT name FROM adresse WHERE id = '".$result[$i]['id']."' LIMIT 1");
    $app->DB->Insert("INSERT INTO partner (adresse, ref,shop,firma, bezeichnung,netto,tage,geloescht,projekt) values ('".$result[$i]['id']."','".$result[$i]['id']."','$shop',1,'".$app->DB->real_escape_string($name)."',0,0,0,0)");
  }
  $adressen = $mlm->MultilevelDownlineTreeID($result[$i]['id'],true);

  $app->DB->Delete("DELETE FROM mlm_downline WHERE adresse='".$result[$i]['id']."'");
  for($j=0;$j<count($adressen);$j++)
  {
    $app->DB->Insert("INSERT INTO mlm_downline (adresse,downline) VALUES ('".$result[$i]['id']."','".$adressen[$j]."')");
  }
}

$app->erp->LogFile("MLM fertig");

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
  $app->erp->LogFile("Partner Export Shop: Keine Shops");
}
$app->erp->LogFile("Partner Export Shop");

?>
