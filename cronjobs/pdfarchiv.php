<?php
set_time_limit(3600);


error_reporting(E_ERROR | E_WARNING | E_PARSE);

include_once(dirname(__FILE__)."/../conf/main.conf.php");
include_once(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include_once(dirname(__FILE__)."/../phpwf/plugins/class.secure.php");
include_once(dirname(__FILE__)."/../phpwf/plugins/class.user.php");
if(file_exists(dirname(__FILE__).'/../conf/user_defined.php'))include_once(dirname(__FILE__).'/../conf/user_defined.php');
if(defined('USEFPDF3') && USEFPDF3)
{
  if(file_exists(__DIR__ .'/../www/lib/pdf/fpdf_3.php'))
  {
    require_once(__DIR__ .'/../www/lib/pdf/fpdf_3.php');
  }else {
    require_once(__DIR__ .'/../www/lib/pdf/fpdf.php');
  }
}
else if(defined('USEFPDF2') && USEFPDF2)
{
  if(file_exists(__DIR__ .'/../www/lib/pdf/fpdf_2.php'))
  {
    require_once(__DIR__ .'/../www/lib/pdf/fpdf_2.php');
  }else {
    require_once(__DIR__ .'/../www/lib/pdf/fpdf.php');
  }
} else {
  require_once(__DIR__ .'/../www/lib/pdf/fpdf.php');
}
include_once(dirname(__FILE__)."/../www/lib/pdf/fpdf_final.php");
include_once(dirname(__FILE__)."/../www/lib/imap.inc.php");
include_once(dirname(__FILE__)."/../www/lib/class.erpapi.php");
include_once(dirname(__FILE__)."/../www/lib/class.remote.php");
include_once(dirname(__FILE__)."/../www/lib/class.httpclient.php");
$aes = '';
$phpversion = (String)phpversion();
if($phpversion{0} == '7' && (int)$phpversion{2} > 0)$aes = '2';
if($aes == 2 && is_file(dirname(__FILE__)."/../www/lib/class.aes".$aes.".php"))
{
  include_once(dirname(__FILE__)."/../www/lib/class.aes".$aes.".php");
}else
  include_once(dirname(__FILE__)."/../www/lib/class.aes.php");
include_once(dirname(__FILE__)."/../www/plugins/phpmailer/class.phpmailer.php");
include_once(dirname(__FILE__)."/../www/plugins/phpmailer/class.smtp.php");

$classes = array('briefpapier','lieferschein','auftrag','anfrage','gutschrift','bestellung','rechnung','mahnwesen');
foreach($classes as $class)
{
  if(file_exists(dirname(__FILE__)."/../www/lib/dokumente/class.".$class."_custom.php"))
  {
    include_once(dirname(__FILE__)."/../www/lib/dokumente/class.".$class."_custom.php");
  }elseif(file_exists(dirname(__FILE__)."/../www/lib/dokumente/class.".$class.".php"))
  {
    include_once(dirname(__FILE__)."/../www/lib/dokumente/class.".$class.".php");
  }
}

/*
include_once(dirname(__FILE__)."/../www/lib/dokumente/class.briefpapier.php");
include_once(dirname(__FILE__)."/../www/lib/dokumente/class.lieferschein.php");
include_once(dirname(__FILE__)."/../www/lib/dokumente/class.auftrag.php");
include_once(dirname(__FILE__)."/../www/lib/dokumente/class.angebot.php");
if(file_exists(dirname(__FILE__)."/../www/lib/dokumente/class.anfrage.php"))include_once(dirname(__FILE__)."/../www/lib/dokumente/class.anfrage.php");
include_once(dirname(__FILE__)."/../www/lib/dokumente/class.gutschrift.php");
include_once(dirname(__FILE__)."/../www/lib/dokumente/class.bestellung.php");
include_once(dirname(__FILE__)."/../www/lib/dokumente/class.rechnung.php");
include_once(dirname(__FILE__)."/../www/lib/dokumente/class.mahnwesen.php");
*/

include_once(dirname(__FILE__)."/../phpwf/plugins/class.string.php");
if(!class_exists('app_t')){
  class app_t
  {
    var $DB;
    var $erp;
    var $User;
    var $mail;
    var $remote;
    var $Secure;
  }
}
//ENDE

if(!isset($app))
{

$app = new app_t();

$conf = new Config();
$app->Conf = $conf;
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass,null,$conf->WFdbport);
$erp = new erpAPI($app);
$app->erp = $erp;
$app->String         = new WawiString();
$app->erp->LogFile("MLM gestartet");

$app->Secure = new Secure($app);
$app->User = new User($app);
if(!defined('FPDF_FONTPATH'))define('FPDF_FONTPATH',dirname(__FILE__)."/../www/lib/pdf/font/");
}
echo "Suche nach nicht archivierten Dokumenten..\r\n";

$dokumente = array('auftrag','angebot','gutschrift','rechnung','bestellung','lieferschein','anfrage');
foreach($dokumente as $table)
{
  
  
  echo $table."\r\n";
  $check = $app->DB->Query("SELECT t.* FROM $table t LEFT JOIN pdfarchiv p ON p.table_id = t.id AND p.table_name = '$table' WHERE isnull(p.id) AND belegnr <> '' AND status <> 'storniert' AND status <> 'angelegt' AND status <> 'angelegta' AND status <> 'a' AND status <> ''");
  while($row = $app->DB->Fetch_Array($check))
  {
    echo $row['id']."\r\n";
    if($table == 'rechnung')
    {
      $mahnwesen = $this->app->DB->Select("SELECT mahnwesen FROM rechnung WHERE id='".$row['id']."' LIMIT 1");
      if($mahnwesen)
      {
        $app->erp->BriefpapierHintergrunddisable = !$app->erp->BriefpapierHintergrunddisable;
        if(class_exists('MahnwesenCustom'))
        {
          $Brief = new MahnwesenCustom($app, $row['projekt']);
        }else{
          $Brief = new Mahnwesen($app, $row['projekt']);
        }
        $Brief->GetRechnung($row['id'], $mahnwesen);
        $tmpfile = $Brief->displayTMP();
        $Brief->ArchiviereDocument();        
        unlink($tmpfile);
        $app->erp->BriefpapierHintergrunddisable = !$app->erp->BriefpapierHintergrunddisable;
        if(class_exists('MahnwesenCustom'))
        {
          $Brief = new MahnwesenCustom($app, $row['projekt']);
        }else{
          $Brief = new Mahnwesen($app, $row['projekt']);
        }
        $Brief->GetRechnung($row['id'], $mahnwesen);
        $tmpfile = $Brief->displayTMP();
        $Brief->ArchiviereDocument();
      }
    }
    $name = ucfirst($table).'PDFCustom';
    if(!class_exists($name))$name = ucfirst($table).'PDF';
    $nameget = 'Get'.ucfirst($table);
    $app->erp->BriefpapierHintergrunddisable = !$app->erp->BriefpapierHintergrunddisable;
    $Brief = new $name($app, $row['projekt']);
    $Brief->$nameget($row['id']);
    $tmpfile = $Brief->displayTMP();
    $Brief->ArchiviereDocument();    
    unlink($tmpfile);
    $app->erp->BriefpapierHintergrunddisable = !$app->erp->BriefpapierHintergrunddisable;
    $Brief = new $name($app, $row['projekt']);
    $Brief->$nameget($row['id']);
    $tmpfile = $Brief->displayTMP();
    $Brief->ArchiviereDocument();
   
  }
  if(method_exists($app->erp, 'canRunCronjob') && !$app->erp->canRunCronjob(['pdfarchiv'])) {
    return;
  }
}
