<?php

use Xentral\Core\ErrorHandler\ErrorHandler;
use Xentral\Core\LegacyConfig\ConfigLoader;
use Xentral\Core\LegacyConfig\Exception\MultiDbConfigNotFoundException;

$memory_limit = @ini_get('memory_limit');
if($memory_limit)
{
  if(strpos($memory_limit, 'M') !== false)
  {
    $memory_limit = str_replace('M','', $memory_limit);
    $memory_limit *= 1024*1024;
  }
  if($memory_limit < 128000000)
  {
    $memory_changed = @ini_set('memory_limit', 512*1024*1024);
  }
}
if(file_exists(dirname(__DIR__).'/phpwf/plugins/class.devtools.php'))include_once(dirname(__DIR__).'/phpwf/plugins/class.devtools.php');


include_once (dirname(__DIR__).'/xentral_autoloader.php');

if(!isset($_GET['module']) || $_GET['module'] != 'api')
{
  if(!(isset($_GET['module']) && isset($_GET['action']) && isset($_GET['cmd']) && $_GET['module'] == 'welcome' && (($_GET['action'] == 'login' && $_GET['cmd'] == 'checkrfid') || $_GET['action'] == 'cronjob' || $_GET['action'] == 'adapterbox')))
    @session_start();
}
error_reporting(E_ERROR);
header("X-Frame-Options: SAMEORIGIN"); // schutz damit wawision nichts externe im browser erlaubt
header("Content-Type: text/html; charset=utf-8");
ini_set("default_charset", 'utf-8');

$missing = false;

$errorHandler = new ErrorHandler();
$errorHandler->register();

include("eproosystem.php");

if(!is_file(dirname(dirname($_SERVER['SCRIPT_FILENAME'])).DIRECTORY_SEPARATOR."conf/user.inc.php"))
        header('Location: ./setup/setup.php');
else {
include(dirname(dirname($_SERVER['SCRIPT_FILENAME'])).DIRECTORY_SEPARATOR."/conf/main.conf.php");
try {
  $config = ConfigLoader::load();
} catch (MultiDbConfigNotFoundException $exception) {
  setcookie('DBSELECTED','',time()-86400);
  throw $exception;
}

$app = new erpooSystem($config);

// layer 2 -> darfst du ueberhaupt?
include("../phpwf/class.session.php");
$session = new Session();
$session->Check($app);
// layer 3 -> nur noch abspielen
include("../phpwf/class.player.php");
$player = new Player();
$player->Run($session);
}
if(isset($app->DB) && isset($app->DB->connection) && $app->DB->connection)$app->DB->Close();

