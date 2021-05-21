<?php
//include("wawision.inc.php");

use Xentral\Core\Installer\Installer;
use Xentral\Core\Installer\InstallerCacheConfig;
use Xentral\Core\Installer\InstallerCacheWriter;
use Xentral\Core\Installer\ClassMapGenerator;
use Xentral\Core\Installer\Psr4ClassNameResolver;
use Xentral\Core\Installer\TableSchemaEnsurer;
use Xentral\Components\Database\DatabaseConfig;

// Nur einfache Fehler melden
error_reporting(E_ERROR | E_COMPILE_ERROR | E_CORE_ERROR | E_RECOVERABLE_ERROR | E_USER_ERROR | E_PARSE);
if(file_exists(__DIR__.'/xentral_autoloader.php')){
  include_once (__DIR__.'/xentral_autoloader.php');
}

include_once("conf/main.conf.php");
include_once("phpwf/plugins/class.mysql.php");
include_once("www/lib/class.erpapi.php");
if(file_exists("www/lib/class.erpapi_custom.php")){
  include_once("www/lib/class.erpapi_custom.php");
}
/*
class app_t
{
  var $DB;
  var $user;
  var $Conf;
}

$app = new app_t();
*/

$config = new Config();

// Delete ServiceMap-CacheFile
$installConf = new InstallerCacheConfig($config->WFuserdata . '/tmp/' . $config->WFdbname);
$serviceCacheFile = $installConf->getServiceCacheFile();
@unlink($serviceCacheFile);

$app = new ApplicationCore();

$DEBUG = 0;

$app->Conf = $config;
$app->DB = new DB($app->Conf->WFdbhost,$app->Conf->WFdbname,$app->Conf->WFdbuser,$app->Conf->WFdbpass, $app, $app->Conf->WFdbport);
if(class_exists('erpAPICustom'))
{
  $erp = new erpAPICustom($app);
}else{
  $erp = new erpAPI($app);
}

echo "STARTE   DB Upgrade\r\n";
$erp->UpgradeDatabase();
echo "ENDE     DB Upgrade\r\n\r\n";

try {
  echo "STARTE   Installer\r\n";

  $resolver = new Psr4ClassNameResolver();
  $resolver->addNamespace('Xentral\\', __DIR__ . '/classes');
  $resolver->excludeFile(__DIR__ . '/classes/bootstrap.php');

  $generator = new ClassMapGenerator($resolver, __DIR__);
  $installer = new Installer($generator, $resolver);
  $writer = new InstallerCacheWriter($installConf, $installer);

  $dbConfig = new DatabaseConfig(
    $app->Conf->WFdbhost,
    $app->Conf->WFdbuser,
    $app->Conf->WFdbpass,
    $app->Conf->WFdbname,
    null,
    $app->Conf->WFdbport
  );
  $tableSchemaCreator = new TableSchemaEnsurer(
    $app->Container->get('SchemaCreator'),
    $installConf,
    $dbConfig
  );

  echo "SCHREIBE ServiceMap\r\n";
  $writer->writeServiceCache();

  echo "SCHREIBE JavascriptMap\r\n";
  $writer->writeJavascriptCache();

  echo "ERZEUGE  Table Schemas\r\n";
  $schemaCollection = $installer->getTableSchemas();
  $tableSchemaCreator->ensureSchemas($schemaCollection);

  echo "ENDE     Installer\r\n";
  //
} catch (Exception $e) {
  echo "FEHLER   " . $e->getMessage() . "\r\n";
}
