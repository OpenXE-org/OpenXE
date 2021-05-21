<?php
use Xentral\Core\LegacyConfig\ConfigLoader;
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
if(file_exists(dirname(__DIR__).'/xentral_autoloader.php'))
{
  include_once dirname(__DIR__).'/xentral_autoloader.php';
}
@date_default_timezone_set('Europe/Berlin');

include_once dirname(__DIR__).'/conf/main.conf.php';
include_once dirname(__DIR__).'/phpwf/plugins/class.mysql.php';

include_once dirname(__DIR__).'/phpwf/plugins/class.secure.php';
include_once dirname(__DIR__).'/phpwf/plugins/class.user.php';
include_once dirname(__DIR__).'/www/lib/imap.inc.php';
include_once dirname(__DIR__).'/www/lib/class.erpapi.php';

if(is_file(dirname(__DIR__).'/www/lib/class.erpapi_custom.php')){
  include_once dirname(__DIR__) . '/www/lib/class.erpapi_custom.php';
}
include_once dirname(__DIR__).'/www/lib/class.httpclient.php';
$aes = '';
$phpversion = PHP_VERSION;
if(strpos($phpversion,'7') === 0 && (int)$phpversion{2} > 0)
{
  $aes = '2';
}
if($aes === '2' && is_file(dirname(__DIR__).'/www/lib/class.aes'.$aes.'.php'))
{
  include_once dirname(__DIR__).'/www/lib/class.aes'.$aes.'.php';
}elseif(is_file(dirname(__DIR__) . '/www/lib/class.aes.php')){
  include_once dirname(__DIR__) . '/www/lib/class.aes.php';
}
include_once dirname(__DIR__).'/www/lib/class.remote.php';
include_once dirname(__DIR__).'/www/plugins/phpmailer/class.phpmailer.php';
include_once dirname(__DIR__).'/www/plugins/phpmailer/class.smtp.php';

class app_t extends ApplicationCore {
  public $DB;
  public $user;
  public $mail;
  public $erp;
  public $remote;
}
$conf = new Config();
$app = new app_t($conf);

$DEBUG = 0;

$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass,null,$conf->WFdbport);
if(class_exists('erpAPICustom')) {
  $erp = new erpAPICustom($app);
}
else{
  $erp = new erpAPI($app);
}
$app->erp = $erp;
$app->Conf = $conf;
if(method_exists($app->erp, 'CheckCronjob') && !$app->erp->CheckCronjob()) {
  exit;
}
$multiDbs = ConfigLoader::loadAllWithActiveCronjobs();
//$multiDbs = $app->getCronjobDbs();
if(empty($multiDbs)) {
  $multiDbs = [$conf];
}
define('FIRSTDB', $conf->WFdbname);
foreach($multiDbs as $multiDbKey => $multiDbConf) {
  $app = new app_t($multiDbConf);
  $app->Conf = $multiDbConf;
  $erp->app = $app;
  $app->erp = $erp;
  $app->DB = new DB($multiDbConf->WFdbhost,$multiDbConf->WFdbname,$multiDbConf->WFdbuser,$multiDbConf->WFdbpass,$app,$multiDbConf->WFdbport);

  $multiDb = $multiDbConf->WFdbname;
  if(method_exists($app->erp, 'CheckCronjob') && !$app->erp->CheckCronjob()) {
    $app->DB->Close();
    continue;
  }
  $app->erp->SetKonfigurationValue('prozessstarter_letzteraufruf',date('Y-m-d H:i:s'));
  usleep(mt_rand(100000,1000000));
  $tasks = $app->DB->SelectArr(
    "SELECT * 
      FROM prozessstarter 
      WHERE aktiv=1 AND parameter <> '' AND typ = 'cronjob'
      ORDER BY art = 'periodisch', 
               IFNULL(letzteausfuerhung,'0000-00-00 00:00:00') = '0000-00-00 00:00:00' DESC, 
               letzteausfuerhung"
  );
  /** @var Prozessstarter $obj */
  $obj = $app->erp->LoadModul('prozessstarter');
  if(!defined('CRONJOBUID')){
    $cronjobuid = $obj->getNewUid();
    define('CRONJOBUID', $cronjobuid);
  }
  else {
    $cronjobuid = CRONJOBUID;
  }

  if(defined('PHP_BINARY') && PHP_BINARY !== ''){
    $php = PHP_BINARY;
  }
  elseif(isset($_SERVER['_']) && strpos($_SERVER['_'],'php') !== false){
    $php = $_SERVER['_'];
  }
  elseif(defined('PHP_BINDIR')){
    $php = PHP_BINDIR . DIRECTORY_SEPARATOR.'php';
  }
  else{
    $php = 'php';
  }
  if(!empty($tasks)) {
    foreach ($tasks as $task) {
      $isValidParameter = $task['parameter'] != '' && strpos($task['parameter'], '..') === false;
      if(!$isValidParameter) {
        continue;
      }
      $run = 0;
      if($DEBUG){
        $app->erp->LogFile('Task: ' . $task['bezeichnung'] . ' ' . $task['art']);
      }

      if($task['art'] === 'periodisch'){
        $task = $app->DB->SelectRow(
          sprintf('SELECT * FROM prozessstarter WHERE id = %d LIMIT 1', $task['id'])
        );
        if(empty($task) || $task['aktiv'] == 0){
          continue;
        }
        //$app->erp->LogFile("Periodisch");
        if($task['letzteausfuerhung'] === null || $task['letzteausfuerhung'] === '0000-00-00 00:00:00') {
          $run = 1;
        }
        else{
          $run = $app->DB->Select(
            sprintf(
              'SELECT IF(DATE_SUB(NOW(),INTERVAL %d MINUTE)>\'%s\',\'1\',\'0\')',
              $task['periode'],
              $task['letzteausfuerhung']
            )
          );
        }
      }
      elseif($task['art'] === 'uhrzeit'){
        $task['startzeit'] = str_replace('0000-00-00', date('Y-m-d'), $task['startzeit']);
        $time = strtotime($task['startzeit']);

        $time_letzte = strtotime($task['letzteausfuerhung']);

        //pro minute maximal
        if(date('H', $time) == date('H') && date('i', $time) == date('i'))// && (date('i',$time_letzte) != date('i')))
        {
          $run = 1;
        }
        else{
          continue;
        }
      }

      // wenn art filter gesetzt ist
      if($task['art_filter'] != ''){
        if(date('N') != $task['art_filter']){
          continue;
        }
      }

      if($run && $task['art'] === 'periodisch'){
        $run = $app->erp->checkCronjobRunning(CRONJOBUID, $task);
      }
      if(!$run) {
        continue;
      }

      $app->erp->setCronjobRunning(CRONJOBUID, $task, true);
      if($DEBUG){
        $app->erp->LogFile('Prozessstarter ' . $task['parameter']);
      }
      //update letzte ausfuerhung
      $app->DB->Update(
        sprintf(
          'UPDATE prozessstarter SET letzteausfuerhung=NOW() WHERE id= %d LIMIT 1',
          $task['id']
        )
      );
      //start
      // wenn das skript laeuft hier abbrechen
      $mutexcounter = $app->DB->Select(
        sprintf(
          'SELECT MAX(mutexcounter) FROM prozessstarter WHERE parameter=\'%s\'',
          $app->DB->real_escape_string($task['parameter'])
        )
      );

      if($mutexcounter > 5){
        $app->DB->Update(
          sprintf(
            'UPDATE prozessstarter SET mutexcounter=0,mutex=0 WHERE parameter=\'%s\' AND mutexcounter > 5',
            $app->DB->real_escape_string($task['parameter'])
          )
        );
        $app->DB->Update(
          sprintf(
            'UPDATE cronjob_starter_running SET active = -1 WHERE task_id = %d AND task_id > 0 ',
            $task['id']
          )
        );
      }
      if($task['typ'] !== 'cronjob'){
        continue;
      }

      if(is_file(__DIR__ . '/' . $task['parameter'] . '.php')) {
        $file = 'command.php';
        if(!file_exists(__DIR__.'/'.$file)) {
          $file = 'starter.php';
        }
        $cmd = 'cd  ' . __DIR__ . ' && ' . $php . ' ' . $file.' ' .
          $task['id'] . ' ' . CRONJOBUID .(!empty($multiDb)?' '.base64_encode($multiDb):''). ' 2>&1';
        $output = [];
        exec($cmd, $output, $returnvar);
        if(method_exists($app->erp, 'checkIfCronjobRunning')) {
          $app->erp->checkIfCronjobRunning(CRONJOBUID, $task);
        }
        $coutput = !empty($output) ? count($output) : 0;
        if($returnvar || $coutput > 0){
          $lastLines = [];
          $startIndex = $coutput > 6 ? $coutput - 6 : 0;
          for ($outputLineInxed = $startIndex; $outputLineInxed < $coutput; $outputLineInxed++) {
            $lastLines[] = $output[$outputLineInxed];
          }
          $app->erp->LogFile(
            [
              'cmd' => $cmd,
              'parameter' => $task['parameter'],
              'returnvar' => $returnvar,
              'lastLines' => $lastLines,
              'output' => $output
            ]
          );
          if(!empty($output)) {
            $app->erp->checkCronjobMemory($output, $task);
          }
        }
        unset($output);
        unset($returnvar);
      }
      else {
        $app->erp->LogFile(
          $app->DB->real_escape_string(
            'Der Prozessstarter ' . $task['parameter'] . ' wurde nicht gefunden'
          )
        );
      }
      if($multiDbConf->WFdbhost != $app->Conf->WFdbhost) {
        $app = new app_t($multiDbConf);
        $app->erp = $erp;
        $app->DB = new DB($multiDbConf->WFdbhost,$multiDbConf->WFdbname,$multiDbConf->WFdbuser,$multiDbConf->WFdbpass, $app,$multiDbConf->WFdbport);
      }
    }
  }
  $app->erp->setCronjobRunning(CRONJOBUID, null, false);
  $app->DB->Close();
}
