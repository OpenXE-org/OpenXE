<?
include(dirname(__FILE__)."/../conf/main.conf.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include(dirname(__FILE__)."/../www/lib/imap.inc.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.phpmailer.php");

class app_t {
  var $DB;
  var $user;
}

$app = new app_t();
$conf = new Config();
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass,null,$conf->WFdbport);

// wenn das skript laeuft hier abbrechen
$arradresse = $app->DB->SelectArr("SELECT id, konto,blz,iban FROM adresse WHERE iban=''");

for($i=0;$i<count($arradresse);$i++)
{
	if($arradresse[$i]['konto']!="" && $arradresse[$i]['blz']!="")
	{
		$iban=make_iban($arradresse[$i]['blz'], $arradresse[$i]['konto']);
		$app->DB->SelectArr("UPDATE adresse SET iban='$iban' WHERE id='".$arradresse[$i]['id']."' LIMIT 1");
		$iban="";
	}	
}

function make_iban($blz, $kontonr) {
  $blz8 = str_pad ( $blz, 8, "0", STR_PAD_RIGHT);
  $kontonr10 = str_pad ( $kontonr, 10, "0", STR_PAD_LEFT);
  $bban = $blz8 . $kontonr10;
  $pruefsumme = $bban . "131400";
  $modulo = (bcmod($pruefsumme,"97"));
  $pruefziffer =str_pad ( 98 - $modulo, 2, "0",STR_PAD_LEFT);
  $iban = "DE" . $pruefziffer . $bban;
  return $iban;
}
?>
