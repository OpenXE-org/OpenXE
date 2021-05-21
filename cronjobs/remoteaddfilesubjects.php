<?php
include("../conf/main.conf.php");
include("../../phpwf/plugins/class.db.php");
include("../lib/imap.inc.php");
include("../lib/class.remote.php");
$aes = '';
$phpversion = (String)phpversion();
if($phpversion{0} == '7' && (int)$phpversion{2} > 0)$aes = '2';
if($aes == 2 && is_file(dirname(__FILE__)."/../www/lib/class.aes".$aes.".php"))
{
  include_once(dirname(__FILE__)."/../www/lib/class.aes".$aes.".php");
}else
  include_once(dirname(__FILE__)."/../www/lib/class.aes.php");
include("../lib/class.httpclient.php");


class app_t {
  var $DB;
  var $user;
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

$app = new app_t();
$conf = new Config();
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass,null,$conf->WFdbport);


$remote= new Remote($app);


$artikel = $app->DB->SelectArr("SELECT id FROM artikel WHERE shopartikel='1' AND projekt='1'");

$id = "1"; //EPROO-SHOP

$time_start = microtime_float();

for($i=0;$i<count($artikel);$i++)
{
  echo "Article ".$artikel[$i][id]."\r\n";
  $remote->RemoteAddFileSubject($id,$artikel[$i][id]);
}
echo "finish...\r\n";
$time_end = microtime_float();
$time = $time_end - $time_start;

echo "duration: $time seconds\n";

?>
