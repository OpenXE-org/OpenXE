<?php
$conf = $app->erp->GetKonfiguration('demo_exporter_cron');

if(empty($conf)){
  return;
}

if(($oConfig = json_decode($conf, false)) === null || (json_last_error() !== JSON_ERROR_NONE)){
  return;
}

if(property_exists($oConfig, 'action') && property_exists($oConfig, 'demo_exporter_config')){

  $app->erp->SetKonfigurationValue('demo_exporter_cron', '');
  /** @var DemoExporter $oDemoExporter */
  $oDemoExporter = $app->loadModule('demoexporter');
  if($oConfig->action === 'RunDemoExporter'){
    $oDemoExporter->RunDemoExporter($oConfig);
  }
}