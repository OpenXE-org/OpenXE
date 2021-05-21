<?php
// Nur einfache Fehler melden
//error_reporting(E_ERROR | E_WARNING | E_PARSE);

include_once("../conf/main.conf.php");
include_once("../phpwf/plugins/class.mysql.php");
include_once("../www/lib/class.erpapi.php");

class app_t {
  var $DB;
  var $user;
}

$app = new app_t();

$DEBUG = 0;


$conf = new Config();
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass,null,$conf->WFdbport);
$erp = new erpAPI($app);


$tables = $app->DB->SelectArr("SHOW TABLES");

foreach($tables as $key=>$rows)
{
	$tabelle =  $rows['Tables_in_wawision'];
  $tmp = $app->DB->SelectArr("SHOW INDEX FROM `".$tabelle."`");
  for($i=0;$i<count($tmp);$i++)
  {
		if($tmp[$i]['Key_name']!="PRIMARY")
		{
			$check = $tmp[$i]['Key_name'];
			if ( preg_match('/^[a-z_]{3,20}$/i', $check) ) 
			{
      	$vorlage[$tabelle][]=$tmp[$i]['Key_name'];
			}
  	}
  }
}   

foreach($vorlage as $key=>$row)
{

	$func = function($value) { return "'".$value."'"; };

	$row = array_map($func, $row);
	
	$tmp = rtrim(implode(",",$row),",");

	echo '$vorlage[\''.$key.'\'] = array('.$tmp.");\r\n";


}


?>
