<?php
/*
include(dirname(__FILE__)."/../conf/main.conf.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include(dirname(__FILE__)."/../phpwf/class.application_core.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi.php");
include(dirname(__FILE__)."/../www/pages/auftrag.php");
include_once(dirname(__DIR__)."/phpwf/plugins/class.secure.php");
include_once(dirname(__DIR__)."/phpwf/plugins/class.user.php");


class app_t extends ApplicationCore {
    var $DB;
    var $erp;
    var $user;
    var $remote;
}
*/
//ENDE

if(empty($app))$app = new app_t();

$conf = new Config();
$app->Conf=$conf;
if(empty($app->DB))$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass,null,$conf->WFdbport);
if(class_exists('erpAPICustom'))
{
    $erp = new erpAPICustom($app);
}else{
    $erp = new erpAPI($app);
}
$app->erp = $erp;
if(empty($app->Secure))$app->Secure = new secure_t();
if(empty($app->User))$app->User = new User($app);


$app->DB->DELETE("DELETE FROM singleshipment_order s WHERE s.status='abgeschlossen'");

$orders = $app->DB->SelectArr("SELECT a.id,s.id as sid FROM auftrag a JOIN singleshipment_order s ON a.id=s.order_id
    WHERE a.status='freigegeben' OR a.status='abgeschlossen'");

foreach ($orders as $order){
    $auftrag = new Auftrag($app,true);
    $auftrag->AuftragVersand($order['id']);

    $status = $app->DB->Select('SELECT status FROM auftrag WHERE id ='.$order['id']);
    if($status==='abgeschlossen'){
        $app->DB->Update("UPDATE singleshipment_order SET status='abgeschlossen' WHERE id=".$order['sid']);
    }else{
        $app->DB->Update("UPDATE singleshipment_order SET status='fehlgeschlagen' WHERE id=".$order['sid']);
    }
}
