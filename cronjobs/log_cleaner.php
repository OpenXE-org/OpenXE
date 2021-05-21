<?php

declare(strict_types=1);

use Xentral\Modules\Log\Service\DatabaseLogService;

/** @var ApplicationCore $app */

/** @var DatabaseLogService $logService */
$logService = $app->Container->get('DatabaseLogService');
$cleanupInterval = (int)$app->erp->Firmendaten('cleaner_logfile_tage');
$logService->removeLogsOlderThan($cleanupInterval);
