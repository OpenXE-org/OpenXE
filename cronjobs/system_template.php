<?php

$timeout = 10;
$fakeLastRun = date('Y-m-j H:i:s', strtotime('-1 days'));
$conf = $app->erp->GetKonfiguration('system_template_configuration_cron');
if(empty($conf)){
  return;
}

if(($oConfig = json_decode($conf)) && property_exists($oConfig, 'action')){

  $app->erp->SetKonfigurationValue('system_template_configuration_cron', '');

// CHECK WHETHER A JOB IS RUNNING
  while ($app->DB->Select("SELECT id FROM prozessstarter WHERE aktiv=1 AND mutex=1 LIMIT 1")) {
    $timeout -= 5;
    usleep(5000000);
    //$app->DB->Update("UPDATE prozessstarter SET aktiv=0 WHERE mutex=0");

    if($timeout <= 0){
      // DISABLE ALL CRON JOBS
      if($oConfig->action === 'RunRestoreJob'){
        $app->DB->Update("UPDATE prozessstarter SET aktiv=0");
      }
      break;
    }
  }
  /** @var SystemTemplates $oSystemTemplates */
  $oSystemTemplates = $app->loadModule('systemtemplates');
  if($oConfig->action === 'RunRestoreJob'){
    $oSystemTemplates->RunRestoreJob($oConfig);
  }
}