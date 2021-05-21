<?php

use Xentral\Modules\Api\Engine\ApiContainer;

if (!defined('API_REQUEST')) {
    define('API_REQUEST', true);
}

if (!class_exists('Config', true)) {
    // Anpassung für Demo-Server; dort liegen Config und Anwendung in unterschiedlichen Verzeichnissen
    include dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME']))) . DIRECTORY_SEPARATOR . 'conf/main.conf.php';
}

if (isset($_SERVER['HTTP_MULTIDB']) && (string)$_SERVER['HTTP_MULTIDB'] !== '') {
    define('MULTIDB', (string)$_SERVER['HTTP_MULTIDB']);
}

return new ApiContainer();
