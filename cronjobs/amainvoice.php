<?php
use Xentral\Modules\AmaInvoice\Scheduler\AmaInvoiceTask;
if(!$app->erp->ModulVorhanden('amainvoice')) {
  return;
}
try {
  /** @var AmaInvoiceTask $amaInvoiceTask */
  $amaInvoiceTask = $app->Container->get('AmaInvoiceTask');
  $amaInvoiceTask->execute();
  $amaInvoiceTask->cleanup();

} catch (\Exception $exception) {
  $amaInvoiceTask->cleanup();
  throw $exception;
}
