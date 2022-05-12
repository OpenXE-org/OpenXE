<?php

use Xentral\Modules\SubscriptionCycle\Scheduler\SubscriptionCycleFullTask;
use Xentral\Modules\SubscriptionCycle\Scheduler\SubscriptionCycleManualJobTask;

error_reporting(E_ERROR);

include_once dirname(__DIR__) . '/xentral_autoloader.php';

if(empty($app) || !($app instanceof ApplicationCore)){
  $app = new ApplicationCore();
}
if(!$app->erp->ModulVorhanden('rechnungslauf')) {
  return;
}
/** @var SubscriptionCycleFullTask $subscriptionCycleFullTask */
$subscriptionCycleFullTask = $app->Container->get('SubscriptionCycleFullTask');
try {
  $subscriptionCycleFullTask->execute();
  $subscriptionCycleFullTask->cleanup();
} catch (Exception $exception) {
  $subscriptionCycleFullTask->cleanup();
  throw new $exception;
}
/** @var SubscriptionCycleManualJobTask $subscriptionCycleManualJobTask */
$subscriptionCycleManualJobTask = $app->Container->get('SubscriptionCycleManualJobTask');
try {
  $subscriptionCycleManualJobTask->execute();
  $subscriptionCycleManualJobTask->cleanup();
} catch (Exception $exception) {
  $subscriptionCycleManualJobTask->cleanup();
}
