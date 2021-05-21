<?php
include("../conf/main.conf.php");
include("../phpwf/plugins/class.db.php");
include("../webroot/lib/imap.inc.php");
include("../webroot/lib/class.erpapi.php");
include("../webroot/lib/class.remote.php");
$aes = '';
$phpversion = (String)phpversion();
if($phpversion{0} == '7' && (int)$phpversion{2} > 0)$aes = '2';
if($aes == 2 && is_file(dirname(__FILE__)."/../www/lib/class.aes".$aes.".php"))
{
  include_once(dirname(__FILE__)."/../www/lib/class.aes".$aes.".php");
}else
  include_once(dirname(__FILE__)."/../www/lib/class.aes.php");
include("../webroot/lib/class.httpclient.php");

$id = "1"; //EPROO-SHOP

class app_t {
  var $DB;
  var $user;
  var $erp;
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

$app = new app_t();
$conf = new Config();
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass,null,$conf->WFdbport);


$erp = new erpAPI($app);
$remote = new Remote($app);
$app->erp = &$erp;
$app->remote = &$remote;

$remote= new Remote($app);


// von hier ************
//ACHTUNG dies macht grad alle artikel bilder usw.. nicht sortiert nach richtigem Shop und datenblaetter auch nicht!
$dateien = $app->DB->SelectArr("SELECT DISTINCT datei FROM datei_stichwoerter WHERE (subjekt!='Druckbild') AND (objekt='Artikel' OR objekt='Kampangen')");

$tmp = $app->remote->RemoteGetFileList($id);


foreach($tmp as $row)
  $checkarray[$row[datei]] = $row[checksum];


$time_start = microtime_float();

for($i=0;$i<count($dateien);$i++)
{
  $fid = $dateien[$i][datei];
  if($checkarray[$fid]!=md5($app->erp->GetDatei($fid)))
  {
    echo "File ".$dateien[$i][datei]."\r\n";
    $remote->RemoteSendFile($id,$dateien[$i][datei]);
  }
}

// bis hier ************
echo "finish...\r\n";
$time_end = microtime_float();
$time = $time_end - $time_start;

echo "duration: $time seconds\n";

?>
