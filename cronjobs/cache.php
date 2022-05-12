<?php
/*
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

include(dirname(__FILE__)."/../conf/main.conf.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include(dirname(__FILE__)."/../www/lib/imap.inc.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi.php");
include(dirname(__FILE__)."/../www/lib/class.remote.php");
include(dirname(__FILE__)."/../www/lib/class.httpclient.php");
include(dirname(__FILE__)."/../www/lib/class.aes.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.phpmailer.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.smtp.php");


class app_t {
  var $DB;
  var $erp;
  var $user;
  var $remote;
}

$app = new app_t();
*/
//ENDE
echo __DIR__ . '/../cache';


$cacheClassFiles = scandir(__DIR__ . '/../cache');

if ($cacheClassFiles) {
	foreach ($cacheClassFiles as $cacheClassFile) {
		if ($cacheClassFile != '.' && $cacheClassFile != '..' && $cacheClassFile != '.svn') {
			require_once(__DIR__ . '/../cache/' . $cacheClassFile);
		}
	}
}


echo "\r\n";
echo "\r\n";
echo "\r\n";
echo "START CACHE GENERATOR\r\n";
echo "\r\n";
echo "\r\n";
echo "\r\n";

$conf = new Config();
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass,null,$conf->WFdbport);
$erp = new erpAPI($app);
$app->erp = $erp;


/* Datenbank aufbauen */

$cacheClasses[] = 'Cache_Projekt';


if ($cacheClasses) {
	foreach ($cacheClasses as $cacheClass) {
		$class = new $cacheClass($app);
		$class->checkCacheRows();
	}
}

unset($cacheClasses);
unset($class);

$cacheTodos = $app->DB->SelectArr('
	SELECT
		*
	FROM
		cache
');

if ($cacheTodos) {
	foreach ($cacheTodos as $cacheTodo) {

		unset($class);
		unset($function);

		$class = 'Cache_' . ucfirst($cacheTodo['table']);
		$function = Cache::getMethodName($cacheTodo);

		if (class_exists($class)) {
			$class = new $class($app, $cacheTodo);

			if (method_exists($class, $function)) {
				call_user_func(array($class, $function));
				$class->save();
			}
		}
	}
}

echo "\r\n";
echo "\r\n";
echo "\r\n";
echo "ENDE CACHE GENERATOR\r\n";

