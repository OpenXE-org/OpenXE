<?php

use Xentral\Core\DependencyInjection\ServiceContainer;
use Xentral\Core\DependencyInjection\ServiceRegistry;
use Xentral\Core\Installer\ClassMapGenerator;
use Xentral\Core\Installer\Installer;
use Xentral\Core\Installer\InstallerCacheConfig;
use Xentral\Core\Installer\InstallerCacheWriter;
use Xentral\Core\Installer\Psr4ClassNameResolver;
use Xentral\Core\LegacyConfig\ConfigLoader;

require dirname(__DIR__) . '/vendor/autoload.php';

//ini_set('display_errors', true);
//ini_set('error_reporting', E_ERROR);
//date_default_timezone_set('UTC');

//set_exception_handler(array('\Xentral\Modules\Api\Error\ErrorHandler', 'handleException'));
//set_error_handler(array('\Xentral\Modules\Api\Error\ErrorHandler', 'handleError'));
//register_shutdown_function();

//define('DEVELOPMENT_MODE', true);

require_once dirname(__DIR__) . '/conf/main.conf.php';

$config = ConfigLoader::load();
$cacheConfig = new InstallerCacheConfig($config->WFuserdata . '/tmp/' . $config->WFdbname);
$serviceCacheFile = $cacheConfig->getServiceCacheFile();
$factoryServiceMap = @include $serviceCacheFile;

if (!is_file($serviceCacheFile)) {

    // Installer ausführen wenn ServiceMap nicht vorhanden ist
    $resolver = new Psr4ClassNameResolver();
    $resolver->addNamespace('Xentral\\', __DIR__);
    $resolver->excludeFile(__DIR__ . '/bootstrap.php');

    $generator = new ClassMapGenerator($resolver, __DIR__);
    $installer = new Installer($generator, $resolver);
    $writer = new InstallerCacheWriter($cacheConfig, $installer);

    $writer->writeServiceCache();
    $writer->writeJavascriptCache();

    // Erzeugte ServiceMap einbinden
    $factoryServiceMap = @include $serviceCacheFile;
    if ($factoryServiceMap === false) {
        throw new RuntimeException(sprintf(
            'Cache-Datei "%s" konnte nicht erzeugt werden. Vermutlich fehlen Schreibrechte in %s',
            $serviceCacheFile, $this->config->getUserDataTempDir()
        ));
    }
}

$registry = new ServiceRegistry();
$registry->add('ServiceContainer', new ServiceContainer($registry));
$registry->addFactories($factoryServiceMap);

return $registry;
