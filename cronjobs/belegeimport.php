<?php
error_reporting(E_ERROR );

include_once dirname(__DIR__) . '/xentral_autoloader.php';

if(empty($app) || !($app instanceof ApplicationCore)) {
  $app = new ApplicationCore();
}
/** @var Belegeimport $belegeimport*/
$belegeimport = $app->erp->LoadModul('belegeimport');

/** @var  BelegeImporterService $belegeimportService */
$belegeimportService = $belegeimport->getImporterService();

if (!$app->DB->Select("SELECT id FROM prozessstarter WHERE mutex = 0 AND parameter = 'belegeimport' AND aktiv = 1")) {
  $app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter + 1 WHERE mutex = 1 AND parameter = 'belegeimport' AND aktiv = 1");
  return;
}
$app->DB->Update("UPDATE prozessstarter SET mutex = 1, mutexcounter = 0 WHERE parameter = 'belegeimport' AND aktiv = 1");

$result = $app->DB->SelectArr("SELECT * FROM belegeimport_running WHERE command IN ('upload','write') ORDER BY id LIMIT 1");

if (count($result) > 0) {
  $r = $result[0];

    if($r['command']=='upload'){

      //Der Prozessstarter bricht den alten Belegeimportprozess nicht ab, der Mutex wird aber irgendwann zurückgesetzt.
      //Damit er nicht weitere Dateien beginnt abzuarbeiten:
      $isWorking = $app->DB->Select("SELECT id FROM belegeimport_running WHERE command IN ('preparing','processing')");
      if(!empty($isWorking)){
        $app->DB->Update("UPDATE prozessstarter SET mutex = 1, mutexcounter = 0 WHERE parameter = 'belegeimport' AND aktiv = 1");
        return;
      }

      //Um Mehrfachausführung im Prozessstarter zu vermeiden
      $app->DB->Update("UPDATE belegeimport_running SET command='preparing' WHERE id = '".$r['id']."'");

      $belegeimportService->BelegeimportRun($r['filename'], $r['art'], $r['status'],false,null,$r['userid']);

      $app->DB->Update("UPDATE belegeimport_running SET command='ready' WHERE id = '".$r['id']."'");
      unlink($r['filename']);
    }
    elseif($r['command']=='write'){

      //Der Prozessstarter bricht den alten Belegeimportprozess nicht ab, der Mutex wird aber irgendwann zurückgesetzt.
      //Damit er nicht weitere Dateien beginnt abzuarbeiten:
      $isWorking = $app->DB->Select("SELECT id FROM belegeimport_running WHERE command IN ('preparing','processing')");
      if(!empty($isWorking)){
        $app->DB->Update("UPDATE prozessstarter SET mutex = 1, mutexcounter = 0 WHERE parameter = 'belegeimport' AND aktiv = 1");
        return;
      }

      //Um Mehrfachausführung im Prozessstarter zu vermeiden
      $app->DB->Update("UPDATE belegeimport_running SET command='processing' WHERE id = '".$r['id']."'");

      $belegeimportService->BelegeimportAll(null, false,$r['userid'],$r['art']);

      $app->DB->Delete("DELETE FROM belegeimport_running WHERE id=".$r['id']);
    }
}

$app->DB->Update("UPDATE prozessstarter SET mutex = 0 , mutexcounter = 0 WHERE (parameter = 'belegeimport' ) AND aktiv = 1");



