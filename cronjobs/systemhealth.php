<?php
$app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter + 1 WHERE mutex = 1 AND parameter = 'systemhealth'  AND aktiv = 1");
if(!$app->DB->Select("SELECT id FROM prozessstarter WHERE mutex = 0 AND parameter = 'systemhealth' AND aktiv = 1")) {
  return;
}
$systemHealth = $app->erp->LoadModul('systemhealth');
if(empty($systemHealth)) {
  return;
}
$app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(),mutex=1,mutexcounter=0 WHERE parameter = 'systemhealth'");
$systemHealth->doCronjob();
$app->DB->Update("UPDATE prozessstarter SET mutexcounter = 0, mutex = 0, letzteausfuerhung = NOW() WHERE mutex = 1 AND parameter = 'systemhealth'  AND aktiv = 1");
