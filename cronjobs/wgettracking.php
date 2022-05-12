<?php
include_once("/var/data/conf/main.conf.php");
include_once("/var/data/phpwf/plugins/class.db.php");
include_once("/var/data/www/lib/class.erpapi.php");


/*
class app_t {
  var $DB;
  var $user;
}
*/

$app = new app_t();

$DEBUG = 0;


$conf = new Config();
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass, null, $conf->WFdbport);
$erp = new erpAPI($app);


//$data = $app->DB->SelectArr("SELECT tracking,lieferschein FROM versand WHERE tracking!=\"\" AND versandunternehmen='dhl' AND download=0 ORDER by id DESC");
$data = $app->DB->SelectArr("SELECT tracking,lieferschein FROM versand WHERE tracking!=\"\" AND versandunternehmen='dhl' AND DATE_SUB(CURDATE(), INTERVAL 30 DAY) >= `versendet_am` ORDER by id DESC");

// und aelter als 2 Wochen

for($i=0;$i<count($data);$i++)
{
$app->DB->Update("UPDATE versand SET download=1 WHERE tracking='".$data[$i]['tracking']."' AND lieferschein='".$data[$i]['lieferschein']."' LIMIT 1");
//echo $data[$i]['tracking'];
if(!is_file("/var/data/userdata/tracking/".$data[$i]['lieferschein']."_".$data[$i]['tracking'].".html"))
{
//echo "/home/eproo/eproo-master/userdata/tracking/".$data[$i]['lieferschein']."_".$data[$i]['tracking'].".pdf";
exec("wget -O - 'http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc=".$data[$i]['tracking']."'  > /var/data/userdata/tracking/".$data[$i]['lieferschein']."_".$data[$i]['tracking'].".html");
}
//echo("wget -O - 'http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc=".$data[$i]['tracking']."'  > test.html && html2ps test.html > test.ps && ps2pdf test.ps /home/eproo/eproo-master/userdata/tracking/".$data[$i]['lieferschein']."_".$data[$i]['tracking'].".pdf");
//echo "\n";
}

?>
