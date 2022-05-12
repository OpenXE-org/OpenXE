<?php
include_once dirname(__DIR__)."/conf/main.conf.php";
include_once dirname(__DIR__)."/phpwf/plugins/class.mysql.php";
include_once dirname(__DIR__)."/www/lib/imap.inc.php";
include_once dirname(__DIR__)."/www/lib/class.erpapi.php";
include_once dirname(__DIR__)."/www/lib/class.remote.php";
include_once dirname(__DIR__)."/www/lib/class.httpclient.php";
if(!class_exists('AES')){
  $aes = '2';

  if(is_file(dirname(__DIR__) . "/www/lib/class.aes2.php")){
    include_once dirname(__DIR__) . "/www/lib/class.aes2.php";
  }else{
    include_once dirname(__DIR__) . "/www/lib/class.aes.php";
  }
}
include_once dirname(__DIR__)."/www/plugins/phpmailer/class.phpmailer.php";
include_once dirname(__DIR__)."/www/plugins/phpmailer/class.smtp.php";
if(!class_exists('app_t')){
  class app_t
  {
    var $DB;
    var $erp;
    var $user;
    var $remote;
  }
}
//ENDE
if(empty($app)){
  $app = new app_t();

  $conf = new Config();
  $app->DB = new DB($conf->WFdbhost, $conf->WFdbname, $conf->WFdbuser, $conf->WFdbpass, null, $conf->WFdbport);
}
if(class_exists('erpAPICustom'))
{
  $erp = new erpAPICustom($app);
}else{
  $erp = new erpAPI($app);
}
$app->erp = $erp;
$app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter + 1 WHERE mutex = 1 AND parameter = 'artikel_zusammenfassen'  AND aktiv = 1");
if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE mutex = 0 AND parameter = 'artikel_zusammenfassen' AND aktiv = 1")){
  return;
}
$artikelArr = $app->DB->Query('SELECT id FROM artikel');
if(!empty($artikelArr))
{
  $counter = 0;
  while($row = $app->DB->Fetch_Assoc($artikelArr))
  {
    if($counter % 50 === 0)
    {
      $app->DB->Update("UPDATE prozessstarter SET mutex = 1 , mutexcounter = 0, letzteausfuerhung = now() WHERE parameter = 'artikel_zusammenfassen'  AND aktiv = 1");
    }
    $app->erp->LagerArtikelZusammenfassen($row['id']);
    $counter++;
  }
  $app->DB->free($artikelArr);
}
$app->DB->Update("UPDATE prozessstarter SET mutex = 0 , mutexcounter = 0 WHERE parameter = 'artikel_zusammenfassen'  AND aktiv = 1");
