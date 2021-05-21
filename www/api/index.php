<?php

ini_set('display_errors', false);
error_reporting(E_ERROR);

require dirname(dirname(__DIR__)) . '/xentral_autoloader.php';
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

define('DEBUG_MODE', false);

$errorHandler = new \Xentral\Modules\Api\Error\ErrorHandler();
$errorHandler->register();

/** @var \Xentral\Modules\Api\Engine\ApiContainer $container */
$container = include __DIR__ . '/bootstrap.php';

$application = new \Xentral\Modules\Api\Engine\ApiApplication($container);
$response = $application->handle();

$response->send();
