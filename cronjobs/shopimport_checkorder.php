<?php
/*
include(dirname(__FILE__)."/../conf/main.conf.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include(dirname(__FILE__)."/../www/lib/imap.inc.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi.php");
//include(dirname(__FILE__)."/../www/lib/class.erpapi_custom.php");
include(dirname(__FILE__)."/../www/lib/class.remote.php");
include(dirname(__FILE__)."/../www/lib/class.httpclient.php");
include(dirname(__FILE__)."/../www/lib/class.aes.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.phpmailer.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.smtp.php");
include(dirname(__FILE__)."/../www/lib/ShopimporterBase.php");
include(dirname(__FILE__)."/../phpwf/class.application_core.php");


class app_t extends ApplicationCore {
  var $DB;
  var $erp;
  var $user;
  var $remote;
}
$app = new app_t();

*/
//ENDE
if(!class_exists('User'))
{
  class User
  {
    public $app;
    public function __construct($app)
    {
      $this->app = $app;
    }

    public function GetName()
    {
      return 'Cronjob';
    }

    public function GetID()
    {
      return 0;
    }

    public function GetFirma()
    {
      return 1;
    }

    public function GetType()
    {
      return 'admin';
    }

    public function DefaultProjekt()
    {
      return $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='1' LIMIT 1");
    }

    public function GetAdresse()
    {
      return 0;
    }

    public function GetUsername()
    {
      return 'Cronjob';
    }

  }
}
if(empty($app->Conf)){
  $conf = new Config();
  $app->Conf = $conf;
}
if(empty($app->DB) || empty($app->DB->connection)) {
  $app->DB = new DB($app->Conf->WFdbhost, $app->Conf->WFdbname, $app->Conf->WFdbuser, $app->Conf->WFdbpass, $app, $app->Conf->WFdbport);
}
if(!isset($app->erp) || !$app->erp) {
  if (class_exists('erpAPICustom')) {
    $erp = new erpAPICustom($app);
  } else {
    $erp = new erpAPI($app);
  }
  $app->erp = $erp;
//$remote = new Remote($app);
//$app->remote= $remote;
}


if(empty($app->User)){
  $app->User = new User($app);
}


$app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter+1 WHERE mutex=1 AND parameter='shopimport_checkorder' AND aktiv=1");
if($app->DB->Select("SELECT mutex FROM prozessstarter WHERE parameter = 'shopimport_checkorder' LIMIT 1") == 1){
  return;
}

$app->DB->Update("UPDATE shopimport_checkorder s 
    JOIN auftrag a ON s.order_id = a.id
    LEFT JOIN rechnung r ON r.id = a.rechnungid
    LEFT JOIN lieferschein l ON l.auftragid = a.id
    SET s.status='order completed'
    WHERE (s.status='paid' OR s.status='canceled' OR s.status='deleted') AND  
      (a.status = 'abgeschlossen' OR a.status='versendet' OR a.status='storniert' OR NOT ISNULL(l.id) OR NOT ISNULL(r.id))");

$orderstocheck = $app->DB->SelectArr("SELECT suo.*, a.adresse,a.projekt FROM shopimport_checkorder suo 
  JOIN (SELECT id FROM shopimport_checkorder 
    WHERE (status='unpaid' OR status='') AND date_last_modified < (NOW() - INTERVAL 30 MINUTE) 
    ORDER BY fetch_counter ASC LIMIT 15) AS suox ON suo.id=suox.id
  LEFT JOIN auftrag a ON a.id = suo.order_id
  ORDER by suo.shop_id");

$lastShopId = null;
$importer = null;
foreach ($orderstocheck as $order){
  $app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex=1,mutexcounter=0 
    WHERE parameter='shopimport_checkorder'");
  $app->DB->Update('UPDATE shopimport_checkorder SET fetch_counter=fetch_counter+1, date_last_modified=NOW() 
    WHERE id='.$order['id']);
  if($lastShopId !== $order['shop_id']){
    $module = $app->DB->Select("SELECT modulename FROM shopexport 
      WHERE id='".$order['shop_id']."' LIMIT 1");
    if(!$app->erp->ModulVorhanden($module))
    {
      continue;
    }
    $importer = $app->erp->LoadModul($module);
    if(!method_exists($importer,'getKonfig'))
    {
      continue;
    }
    $importer->getKonfig($order['shop_id'], '');
    $lastShopId = $order['shop_id'];
  }
  if(!$importer->canGetOrderStatus()){
    continue;
  }

  $status = $order['status'];

  $newOrderData = $importer->ImportGetOrderStatus($order['ext_order']);
	if($newOrderData ['status'] === 'paid'){
		$status = 'paid';
		if(!empty($newOrderData['warenkorb']) || !empty($newOrderData['warenkorbjson'])){
			if(isset($shopImportedOrder['jsonencoded']) && $newOrderData['jsonencoded'])
			{
				$shopOrder = json_decode(base64_decode($newOrderData['warenkorbjson']), true);
			}else{
				$shopOrder = unserialize(base64_decode($newOrderData['warenkorb']));
			}
			if(empty($shopOrder)){
				continue;
			}
			$app->DB->Delete("DELETE FROM auftrag_position WHERE auftrag='".$order['order_id']."'");
			$auftragspositionen = $app->DB->SelectArr("SELECT id FROM auftrag_position WHERE auftrag='".$order['order_id']."'");
			foreach ($auftragspositionen as $position){
				$app->erp->DeleteBelegPosition('auftrag', $position['id']);
			}
			$app->erp->ImportAuftrag($order['adresse'], $shopOrder, $order['projekt'], $order['shop_id'], $order['order_id']);
      $app->erp->AuftragProtokoll($order['order_id'], 'Kaufabwicklung abgeschlossen, Auftrag wurde nachimportiert');
		}
		$app->DB->Update("UPDATE shopimport_checkorder SET status='$status', date_last_modified=NOW()
    WHERE id=".$order['id']);
	}elseif($newOrderData ['orderStatus'] === 'deleted' || $newOrderData['orderStatus'] === 'canceled'){
		//TODO storno
	}

}
$app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 0,mutexcounter=0 
  WHERE parameter = 'shopimport_checkorder'");
