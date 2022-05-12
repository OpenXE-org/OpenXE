<?

include_once("/home/eproo/eproo-master/app/main/conf/main.conf.php");
include_once("/home/eproo/eproo-master/app/main/phpwf/plugins/class.db.php");
include_once("/home/eproo/eproo-master/app/main/www/lib/class.erpapi.php");



class app_t {
  var $DB;
  var $user;
}

$app = new app_t();

$DEBUG = 0;


$conf = new Config();
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass,null,$conf->WFdbport);
$erp = new erpAPI($app);

// <= PHP 5
$cvs = file_get_contents('kontoauszuege_zahlungseingang-1_12_10_2010FEHLER-2_v002.csv', true);
$db_array = preg_split("/(\r\n)+|(\n|\r)+/",$cvs);
exit;

//$data = $app->DB->SelectArr("SELECT kontoauszuege, id, betrag, DATE_FORMAT(datum,'%Y-%m') as datum FROM `kontoauszuege_zahlungseingang` ke WHERE ke.datum < '2010-10-12' AND ke.objekt='rechnung'");

for($i=1;$i<count($db_array);$i++)
{
$zeile = $db_array[$i];
$datensatz = split(';',$zeile);

//print_r($datensatz);
/*    [0] => 2010-03-01
    [1] => 46.85
    [2] => Tobias Göpel
    [3] => 49
    [4] => 1800
    [5] => 1141
*/

//$haben = $data[$i]['betrag'];
//$datum = $data[$i]['datum'];
if($datensatz[5] > 0 && is_numeric($datensatz[5]))
{
  $app->DB->Update("UPDATE kontoauszuege_zahlungseingang SET kontoauszuege='{$datensatz[5]}' WHERE id='{$datensatz[3]}' AND betrag='{$datensatz[1]}' LIMIT 1");
  //echo("UPDATE kontoauszuege_zahlungseingang SET kontoauszuege='{$datensatz[5]}' WHERE id='{$datensatz[3]}' AND betrag='{$datensatz[1]}' LIMIT 1");
  $treffer++;
}
/*
$kontoauszug_count = $app->DB->Select("SELECT COUNT(id) FROM kontoauszuege WHERE haben='$haben' AND DATE_FORMAT(buchung,'%Y-%m')='$datum' AND konto='{$data[$i]['kontoauszuege']}'");
$kontoauszug = $app->DB->Select("SELECT id FROM kontoauszuege WHERE haben='$haben' AND DATE_FORMAT(buchung,'%Y-%m')='$datum' AND konto='{$data[$i]['kontoauszuege']}'");

if($kontoauszug_count!=1)
{
  $kontoauszug="FEHLT!";
  $fehlt++;
} else {
  //$app->DB->Update("UPDATE kontoauszuege_zahlungseingang SET kontoauszuege='$kontoauszug' WHERE id='{$data[$i]['id']}' LIMIT 1");
  echo("UPDATE kontoauszuege_zahlungseingang SET kontoauszuege='$kontoauszug' WHERE id='{$data[$i]['id']}' LIMIT 1");
}
*/
//echo $i." ".$datum." $haben $kontoauszug\n\r";
$gesamt++;

}

echo "gesamt: $gesamt treffer: $treffer";

?>
