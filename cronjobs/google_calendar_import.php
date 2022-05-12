<?php

declare(strict_types = 1);

use Xentral\Components\Logger\Logger;
use Xentral\Modules\GoogleApi\Service\GoogleAccountGateway;
use Xentral\Modules\GoogleCalendar\Client\GoogleCalendarClientFactory;
use Xentral\Modules\GoogleCalendar\Service\GoogleCalendarSynchronizer;
use Xentral\Modules\GoogleCalendar\Task\GoogleCalendarSynchronizerTask;
use Xentral\Modules\User\Service\UserConfigService;

/** @var $app */

/** @var Logger $logger */
$logger = $app->Container->get('Logger');

if (!$app->Container->has('GoogleAccountGateway')) {
    $logger->error('Cannot run Google Sync Importer: GoogleAccountGateway required');
}
if (!$app->Container->has('GoogleCalendarSynchronizer')) {
    $logger->error('Cannot run Google Sync Importer: GoogleCalendarSynchronizer required');
}
if (!$app->Container->has('GoogleCalendarClientFactory')) {
    $logger->error('Cannot run Google Sync Importer: GoogleCalendarClientFactory required');
}
if (!$app->Container->has('UserConfigService')) {
    $logger->error('Cannot run Google Sync Importer: UserConfigService required');
}

/** @var UserConfigService $userConfig */
$userConfig = $app->Container->get('UserConfigService');
/** @var GoogleAccountGateway $apiGateway */
$gateway = $app->Container->get('GoogleAccountGateway');
/** @var GoogleCalendarClientFactory $apiGateway */
$factory = $app->Container->get('GoogleCalendarClientFactory');
/** @var GoogleCalendarSynchronizer $syncService */
$synchronizer = $app->Container->get('GoogleCalendarSynchronizer');

$importer = new GoogleCalendarSynchronizerTask($gateway, $factory, $synchronizer, $userConfig);
$importer->setLogger($logger);
$importer->execute();
