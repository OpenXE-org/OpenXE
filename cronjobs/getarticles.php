<?php
/*
include(dirname(__FILE__)."/../conf/main.conf.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include(dirname(__FILE__)."/../www/lib/imap.inc.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi_custom.php");
include(dirname(__FILE__)."/../www/lib/class.remote.php");
include(dirname(__FILE__)."/../www/lib/class.httpclient.php");
include(dirname(__FILE__)."/../www/lib/class.aes.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.phpmailer.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.smtp.php");



class app_t {
  var $DB;
  var $erp;
  var $user;
  var $remote;
}
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
if(empty($app->DB) || empty($app->DB->connection)){
  $app->DB = new DB($app->Conf->WFdbhost, $app->Conf->WFdbname, $app->Conf->WFdbuser, $app->Conf->WFdbpass, $app, $app->Conf->WFdbport);
}
if(!isset($app->erp) || !$app->erp) {
  if (class_exists('erpAPICustom')) {
    $erp = new erpAPICustom($app);
  } else {
    $erp = new erpAPI($app);
  }
//$remote = new Remote($app);
  $app->erp = $erp;
//$app->remote= $remote;
}

if (is_file(dirname(__DIR__) . '/www/lib/class.remote_custom.php')) {
  if(!class_exists('RemoteCustom')){
    require_once dirname(__DIR__) . '/www/lib/class.remote_custom.php';
  }
  $app->remote = new RemoteCustom($app);
} else {
  $app->remote = new Remote($app);
}

if(empty($app->Conf)){
  $app->Conf = $conf;
}
if(empty($app->User)){
  $app->User = new User($app);
}
$app->erp->LogFile('Cronjob Artikelimport Start');
$app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter + 1 WHERE mutex = 1 AND parameter = 'getarticles' AND aktiv = 1");

if($app->DB->Select("SELECT mutex FROM prozessstarter WHERE parameter = 'getarticles' LIMIT 1") == 1){
  return;
}
$check = $app->DB->Select('SELECT id FROM shopexport_getarticles LIMIT 1');
$app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 1,mutexcounter=0 WHERE parameter = 'getarticles'");
$anz = 0;
while($check > 0)
{
  $shopartikel = $app->DB->Query('SELECT g.id,g.shop,g.nummer FROM shopexport_getarticles g INNER JOIN shopexport s ON g.shop = s.id ORDER by g.id LIMIT 50');
  while($row = $app->DB->Fetch_Array($shopartikel))
  {
    if($app->DB->Select("SELECT id FROM shopexport_getarticles WHERE id = '".$row['id']."' LIMIT 1"))
    {
      $anz++;
      $app->remote->RemoteGetArticle($row['shop'],$row['nummer'], true);
      $app->DB->Delete("DELETE FROM shopexport_getarticles WHERE shop='".$row['shop']."' AND nummer='".$row['nummer']."'");
      $app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 1,mutexcounter=0 WHERE parameter = 'getarticles'");
    }
  }
  //sleep(20); // das performance nicht total spinnt
  $check = $app->DB->Select('SELECT sg.id FROM shopexport_getarticles sg JOIN shopexport s ON sg.shop=s.id  LIMIT 1');
}
$app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 0,mutexcounter=0 WHERE parameter = 'getarticles'");
$app->erp->LogFile('Cronjob Artikelimport Ende '.$anz.' importiert');

