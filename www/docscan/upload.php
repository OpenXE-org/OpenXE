<?php

use Sabre\DAV\Auth\Plugin as AuthPlugin;
use Sabre\DAV\Browser\GuessContentType;
use Sabre\DAV\Locks\Backend\File;
use Sabre\DAV\Locks\Plugin;
use Sabre\DAV\Server;

include_once dirname(dirname(__DIR__)) . '/xentral_autoloader.php';
require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

/*
This is the best starting point if you're just interested in setting up a fileserver.

Make sure that the 'public' and 'tmpdata' exists, with write permissions
for your server.
*/

// settings
date_default_timezone_set('Europe/Berlin');
$tmpDir = sys_get_temp_dir();

// If you want to run the SabreDAV server in a custom location (using mod_rewrite for instance)
// You can override the baseUri here.
// $baseUri = '/';

// Files we need

// Create the root node
$app = new ApplicationCore();
$root = new DocscanRoot($app);

// The rootnode needs in turn to be passed to the server class
$server = new Server($root);

$script = basename(__FILE__);
$base = $_SERVER['REQUEST_URI'];
$base = substr($base, 0, strpos($base, $script) + strlen($script) + 1);

if (isset($base)) {
    $server->setBaseUri($base);
}

// Support for LOCK and UNLOCK
$lockBackend = new File($tmpDir . '/locksdb');
$lockPlugin = new Plugin($lockBackend);
$server->addPlugin($lockPlugin);

// Support for html frontend
// may be used for debugging purposes
//$browser = new \Sabre\DAV\Browser\Plugin();
//$server->addPlugin($browser);

// Automatically guess (some) contenttypes, based on extesion
$server->addPlugin(new GuessContentType());

// Authentication backend
//$authBackend = new \Sabre\DAV\Auth\Backend\File('.htdigest');
$authBackend = new DocscanAuth($app->DB);
$auth = new AuthPlugin($authBackend);
$server->addPlugin($auth);

// Temporary file filter
//$tempFF = new \Sabre\DAV\TemporaryFileFilterPlugin($tmpDir);
//$server->addPlugin($tempFF);

// And off we go!
$server->exec();
