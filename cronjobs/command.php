<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT ^ E_WARNING);
@date_default_timezone_set('Europe/Berlin');
define('USEFPDF3', true);

if ($argc > 3 && !empty($argv[3])) {
  $multiDb = @base64_decode($argv[3]);
  if (!empty($multiDb) && !defined('MULTIDB')) {
    define('MULTIDB', $multiDb);
  }
}

$fromstarter2 = 0;
if ($argc > 1) {
  if ($argv[1] && (int)$argv[1] > 0) {
    $fromstarter2 = (int)$argv[1];
  }
}
define('FROMSTARTER2', $fromstarter2 > 0);

require_once dirname(__DIR__) . '/xentral_autoloader.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';


$memory_limit = @ini_get('memory_limit');
if ($memory_limit && strpos($memory_limit, 'M') !== false) {
  $memory_limit = str_replace('M', '', $memory_limit);
  $memory_limit *= 1024 * 1024;
}
$max_execution_time = @ini_get('max_execution_time');
if ((string)$memory_limit !== '') {
  $memory_limit = (int)$memory_limit;
}
class app_t extends ApplicationCore {
  public $DB;
  public $user;
}
$conf = Xentral\Core\LegacyConfig\ConfigLoader::load();
$app = new ApplicationCore($conf);
$DEBUG = 0;

$app->DB = new DB($conf->WFdbhost, $conf->WFdbname, $conf->WFdbuser, $conf->WFdbpass, null, $conf->WFdbport);


if (class_exists('erpAPICustom')) {
  $erp = new erpAPICustom($app);
} else {
  $erp = new erpAPI($app);
}
$app->erp = $erp;//new erpAPI($app);

if ($argc > 2 && !empty($argv[2])) {
  define('CRONJOBUID', $argv[2]);
} else {
  /** @var Prozessstarter $processStarter */
  $processStarter = $app->erp->LoadModul('prozessstarter');
  define('CRONJOBUID', $processStarter->getNewUid());
}

$app->erp->SetKonfigurationValue('system_phpversion_cli', phpversion());
$app->erp->SetKonfigurationValue('system_cronjob_memory_limit', $memory_limit);
$app->erp->SetKonfigurationValue('system_cronjob_max_execution_time', $max_execution_time);
$app->erp->SetKonfigurationValue('system_cronjob_php_ini_loaded_file', php_ini_loaded_file());
$app->erp->SetKonfigurationValue('system_cronjob_get_current_user', get_current_user());
ob_start();
phpinfo();
$phpinfo = ob_get_contents();
ob_end_clean();
$app->erp->SetKonfigurationValue('system_cronjob_phpinfo', $app->DB->real_escape_string($phpinfo));

if (method_exists($app->erp, 'CheckCronjob') && !$app->erp->CheckCronjob()) {
  $app->DB->Close();
  exit;
}
$firmendatenid = $app->DB->Select('SELECT MAX(id) FROM firmendaten LIMIT 1');

$benutzername = $app->DB->Select("SELECT benutzername FROM firmendaten WHERE id='" . $firmendatenid . "' LIMIT 1");
$passwort = $app->DB->Select("SELECT passwort FROM firmendaten WHERE id='" . $firmendatenid . "' LIMIT 1");
$host = $app->DB->Select("SELECT host FROM firmendaten WHERE id='" . $firmendatenid . "' LIMIT 1");
$port = $app->DB->Select("SELECT port FROM firmendaten WHERE id='" . $firmendatenid . "' LIMIT 1");
$mailssl = $app->DB->Select("SELECT mailssl FROM firmendaten WHERE id='" . $firmendatenid . "' LIMIT 1");
$noauth = $app->erp->Firmendaten('noauth');

$app->mail = new PHPMailer($app);
$app->mail->CharSet = 'UTF-8';
//$app->mail->PluginDir="plugins/phpmailer/";
$app->mail->IsSMTP();

if ($noauth == '1') {
  $app->mail->SMTPAuth = false;
} else {
  $app->mail->SMTPAuth = true;
}
if($mailssl == 1){
  $app->mail->SMTPSecure = 'tls';
}else if($mailssl == 2){
  $app->mail->SMTPSecure = 'ssl';
}

$app->mail->Host = $host;
$app->mail->Port = $port;                   // set the SMTP port for the GMAIL server

$app->mail->Username = $benutzername;
$app->mail->Password = $passwort;


$app->erp->SetKonfigurationValue('prozessstarter_letzteraufruf', date('Y-m-d H:i:s'));

/** @var \Xentral\Modules\SystemHealth\Service\SystemHealthService $service */
$systemHealthService = $app->Container->get('SystemHealthService');

if ($DEBUG) {
  $app->erp->LogFile('starter.php');
}

$task = $app->DB->SelectArr(
  "SELECT * 
    FROM prozessstarter 
    WHERE aktiv='1' AND typ = 'cronjob' " . ($fromstarter2 ? " AND id = '$fromstarter2' " : '') . " AND parameter <> ''
    ORDER BY art = 'periodisch', 
    IFNULL(letzteausfuerhung,'0000-00-00 00:00:00') = '0000-00-00 00:00:00' DESC, 
    letzteausfuerhung"
);
if ($task) {
  $ctask = count($task);
  for ($task_index = 0; $task_index < $ctask; $task_index++) {
    $isValidParameter = $task[$task_index]['parameter'] != ''
      && strpos($task[$task_index]['parameter'], '..') === false;
    if (!$isValidParameter) {
      continue;
    }

    try {
      $freeDiskSpace = $systemHealthService->getDiskFree('');
      if($freeDiskSpace !== false){
        $freeDiskSpaceInMegabyte = $freeDiskSpace / (1024 * 1024);
        if($freeDiskSpaceInMegabyte < 512 && $task[$task_index]['parameter'] != 'cleaner'){
          continue;
        }
      }
    } catch (Exception $e) {
      $app->erp->LogFile('can not evaluate disk space for cronjob: ' . $e->getMessage());
    }

    if ($fromstarter2) {
      $run = 1;
    } else {
      $run = 0;
      if ($DEBUG) {
        $app->erp->LogFile('Task: ' . $task[$task_index]['bezeichnung'] . ' ' . $task[$task_index]['art']);
      }

      if ($task[$task_index]['art'] === 'periodisch') {
        if (empty($task[$task_index]) || $task[$task_index]['aktiv'] == 0) {
          continue;
        }
        //$app->erp->LogFile("Periodisch");
        $task[$task_index] = $app->DB->SelectRow(
          sprintf(
            'SELECT * FROM prozessstarter WHERE id = %d LIMIT 1',
            $task[$task_index]['id']
          )
        );
        if ($task[$task_index]['letzteausfuerhung'] === null
          || $task[$task_index]['letzteausfuerhung'] === '0000-00-00 00:00:00') {
          $run = 1;
        } else {
          $run = $app->DB->Select("SELECT IF(DATE_SUB(NOW(),INTERVAL {$task[$task_index]['periode']} MINUTE)>'{$task[$task_index]['letzteausfuerhung']}','1','0')");
        }
      }

      if ($task[$task_index]['art'] === 'uhrzeit') {
        $task[$task_index]['startzeit'] = str_replace('0000-00-00', date('Y-m-d'), $task[$task_index]['startzeit']);
        $time = strtotime($task[$task_index]['startzeit']);

        $time_letzte = strtotime($task[$task_index]['letzteausfuerhung']);

        //pro minute maximal
        if (date('H', $time) === date('H') && date('i', $time) === date('i'))// && (date('i',$time_letzte) != date('i')))
        {
          $run = 1;
        } else {
          continue;
        }
      }

      // wenn art filter gesetzt ist
      if ($task[$task_index]['art_filter'] != '') {
        if (date('N') != $task[$task_index]['art_filter']) {
          continue;
        }
      }

      if ($run == 1 && $task[$task_index]['art'] === 'periodisch') {
        $run = $app->erp->checkCronjobRunning(CRONJOBUID, $task[$task_index]);
      }
    }
    if (!$run) {
      continue;
    }
    if (!$fromstarter2) {
      $app->erp->setCronjobRunning(CRONJOBUID, $task[$task_index], true);
    }
    if ($DEBUG) {
      $app->erp->LogFile('Prozessstarter ' . $task[$task_index]['parameter']);
    }
    //update letzte ausfuerhung
    $app->DB->Update(
      sprintf(
        'UPDATE prozessstarter SET letzteausfuerhung=NOW() WHERE id=%d LIMIT 1',
        $task[$task_index]['id']
      )
    );
    //start
    // wenn das skript laeuft hier abbrechen
    $mutexcounter = $app->DB->Select(
      sprintf(
        "SELECT MAX(mutexcounter) FROM prozessstarter WHERE parameter='%s'",
        $task[$task_index]['parameter']
      )
    );
    if ($mutexcounter > 1) {
      $app->erp->ProzessstarterStatus('mutex' . $mutexcounter, $task[$task_index]['id']);
    }
    if ($mutexcounter > 5) {
      $app->DB->Update(
        "UPDATE prozessstarter 
          SET mutexcounter=0, `mutex`=0 
          WHERE parameter='" . $task[$task_index]['parameter'] . "' AND aktiv = 1 AND mutexcounter > 5"
      );
      $app->DB->Update(
        sprintf(
          'UPDATE cronjob_starter_running SET active = -1 WHERE task_id = %d AND task_id > 0 ',
          $task[$task_index]['id']
        )
      );
    }
    if ($task[$task_index]['typ'] !== 'cronjob') {
      continue;
    }

    if (is_file(__DIR__ . '/' . $task[$task_index]['parameter'] . '.php')) {
      try {
        $app->erp->ProzessstarterStatus('gestartet', $task[$task_index]['id']);
        $oldApp = $app;
        $app->erp->setCronjobRunning(CRONJOBUID, $task[$task_index], true);
        include __DIR__ . '/' . $task[$task_index]['parameter'] . '.php';
        $app = $oldApp;
        $app->erp->setCronjobRunning(CRONJOBUID, $task[$task_index], false);
        $app->erp->ProzessstarterStatus('abgeschlossen', $task[$task_index]['id']);
        $app->erp->ProzessstarterStatus('', 0);
      } catch (Exception $e) {
        $app->erp->LogFile(
          $app->DB->real_escape_string(
            'Prozessstarter Fehler bei Aufruf des Moduls ' . $task[$task_index]['parameter'] . ': ' . $e->getMessage()
          )
        );
      }
    } else {
      $app->erp->LogFile(
        $app->DB->real_escape_string(
          'Der Prozessstarter ' . $task[$task_index]['parameter'] . ' wurde nicht gefunden'
        )
      );
    }
  }
}
if (!FROMSTARTER2) {
  $app->erp->setCronjobRunning(CRONJOBUID, null, false);
}
$app->DB->Close();
