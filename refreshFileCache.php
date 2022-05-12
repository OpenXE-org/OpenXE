<?php

use Xentral\Core\Installer\Installer;
use Xentral\Core\Installer\InstallerCacheConfig;
use Xentral\Core\Installer\InstallerCacheWriter;
use Xentral\Core\Installer\ClassMapGenerator;
use Xentral\Core\Installer\Psr4ClassNameResolver;

error_reporting(E_ERROR | E_COMPILE_ERROR | E_CORE_ERROR | E_RECOVERABLE_ERROR | E_USER_ERROR | E_PARSE);

if(file_exists(__DIR__.'/xentral_autoloader.php')){
  include_once (__DIR__.'/xentral_autoloader.php');
}

$config = new Config();
$installerCacheConfig = new InstallerCacheConfig($config->WFuserdata . '/tmp/' . $config->WFdbname);

// delete cache files
$serviceCacheFile = $installerCacheConfig->getServiceCacheFile();
$javascriptCacheFile = $installerCacheConfig->getJavascriptCacheFile();
@unlink($serviceCacheFile);
@unlink($javascriptCacheFile);

// create new cache
try {
  echo "START   File Cache Generator\r\n";

  $resolver = new Psr4ClassNameResolver();
  $resolver->addNamespace('Xentral\\', __DIR__ . '/classes');
  $resolver->excludeFile(__DIR__ . '/classes/bootstrap.php');
  $generator = new ClassMapGenerator($resolver, __DIR__);
  $installer = new Installer($generator, $resolver);
  $writer = new InstallerCacheWriter($installerCacheConfig, $installer);

  echo "WRITING ServiceMap\r\n";
  $writer->writeServiceCache();

  echo "WRITING JavascriptMap\r\n";
  $writer->writeJavascriptCache();

  echo "FINISHED     File Cache Generator\r\n";
} catch (Exception $e) {
  echo "ERROR   " . $e->getMessage() . "\r\n";
}
