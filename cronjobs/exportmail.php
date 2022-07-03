<?php
include_once("/home/eproo/eproo-master/app/main/conf/main.conf.php");
include_once("/home/eproo/eproo-master/app/main/phpwf/plugins/class.db.php");
include_once("/home/eproo/eproo-master/app/main/webroot/lib/imap.inc.php");
include_once("/home/eproo/eproo-master/app/main/webroot/lib/class.erpapi.php");

class app_t {
  var $DB;
  var $user;
}

$app = new app_t();

$DEBUG = 0;


$conf = new Config();
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass,null,$conf->WFdbport);
$erp = new erpAPI($app);

$firmendatenid = $app->DB->Select("SELECT MAX(id) FROM firmendaten LIMIT 1");

$erp->ExportlinkZahlungsmail();

?>
