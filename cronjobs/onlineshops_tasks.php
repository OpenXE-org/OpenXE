<?php

/*
include_once dirname(__DIR__) . '/conf/main.conf.php';
include_once dirname(__DIR__).'/phpwf/plugins/class.mysql.php';
include_once dirname(__DIR__).'/www/lib/imap.inc.php';
include_once dirname(__DIR__).'/phpwf/class.application_core.php';
include_once dirname(__DIR__).'/www/lib/class.erpapi.php';
//include(dirname(__FILE__)."/../www/lib/class.erpapi_custom.php");
include_once dirname(__DIR__).'/www/lib/class.remote.php';
include_once dirname(__DIR__).'/www/lib/class.httpclient.php';
include_once dirname(__DIR__).'/www/lib/class.aes.php';
include_once dirname(__DIR__).'/www/plugins/phpmailer/class.phpmailer.php';
include_once dirname(__DIR__).'/www/plugins/phpmailer/class.smtp.php';
include_once dirname(__DIR__).'/www/lib/ShopimporterBase.php';

$app = new ApplicationCore();
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
if(empty($app->DB)){
  $app->DB = new DB($app->Conf->WFdbhost, $app->Conf->WFdbname, $app->Conf->WFdbuser, $app->Conf->WFdbpass, $app, $app->Conf->WFdbport);
}
if(empty($app->erp)) {
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

$app->DB->Update(
  "UPDATE `prozessstarter` 
  SET `mutexcounter` = `mutexcounter`+1 
  WHERE `mutex`=1 AND `parameter`='onlineshops_tasks' AND `aktiv`=1"
);

$app->DB->Update(
  "UPDATE `prozessstarter` 
  SET `mutexcounter` = 0, `mutex` = 0
  WHERE `mutex`=1 AND `parameter`='onlineshops_tasks' AND `aktiv`=1 AND `mutexcounter` > 5"
);
if(
  $app->DB->Select(
    "SELECT `mutex` FROM `prozessstarter` WHERE `parameter` = 'onlineshops_tasks' AND `aktiv` = 1 LIMIT 1"
  ) == 1
) {
  return;
}
$app->DB->Update(
  "UPDATE `prozessstarter` 
  SET `letzteausfuerhung`=NOW(), `mutex` = 1,`mutexcounter`=0 
  WHERE `parameter` = 'onlineshops_tasks' AND `aktiv` = 1"
);
$onlineShopTask = $app->DB->SelectRow(
  "SELECT * 
  FROM `onlineshops_tasks` 
  WHERE `counter` < 5 AND `status`<> 'stalled' AND `command` <> '' 
  ORDER BY `counter` 
  LIMIT 1"
);
if(!empty($onlineShopTask)){
  $app->DB->Update(
    sprintf('UPDATE `onlineshops_tasks` SET `counter`=`counter`+1 WHERE `id`= %d', $onlineShopTask['id'])
  );
  $module = $app->DB->Select(
    sprintf('SELECT `modulename` FROM `shopexport` WHERE `id`= %d LIMIT 1', $onlineShopTask['shop_id'])
  );
  $command = $onlineShopTask['command'];
  if(!empty($module) && $onlineShopTask['status'] === 'inactive' && $app->erp->ModulVorhanden($module)) {
    $app->DB->Update(
      sprintf("UPDATE `onlineshops_tasks` SET `status`= 'running' WHERE `id` = %d", $onlineShopTask['id'])
    );
    try {
      $app->remote->RemoteCommand($onlineShopTask['shop_id'], $command);
    }
    catch (Exception $e) {
      $app->erp->LogFile(['error'=>$e->getMessage()]);
    }
  }

  if($onlineShopTask['counter'] >= 4){
    $app->DB->Update(
      sprintf("UPDATE `onlineshops_tasks` SET `status`= 'stalled' WHERE `id` = %d", $onlineShopTask['id'])
    );
  }
  else{
    $app->DB->Update(sprintf('DELETE FROM `onlineshops_tasks` WHERE `id` = %d', $onlineShopTask['id']));
  }
}
$app->DB->Update(
  "UPDATE `prozessstarter` 
  SET `letzteausfuerhung`=NOW(), `mutex` = 0,`mutexcounter`=0 
  WHERE `parameter` = 'onlineshops_tasks'"
);