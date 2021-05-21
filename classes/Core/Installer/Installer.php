<?php

namespace Xentral\Core\Installer;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use RuntimeException;
use Xentral\Components\SchemaCreator\Collection\SchemaCollection;

final class Installer
{
    /** @var ClassMapGenerator $classMapGenerator */
    private $classMapGenerator;

    /** @var Psr4ClassNameResolver $classNameResolver */
    private $classNameResolver;

    /** @var array $classMap */
    private $classMap = [];

    /** @var array $bootstrapClasses */
    private $bootstrapClasses = [];

    /** @var array */
    private $services = [];

    /** @var array $javascript */
    private $javascript = [];

    /** @var string $classDir */
    private $classDir;

    /**
     * @param ClassMapGenerator $generator
     */
    public function __construct(ClassMapGenerator $generator, Psr4ClassNameResolver $resolver)
    {
        $this->classMapGenerator = $generator;
        $this->classNameResolver = $resolver;
        $this->classDir = dirname(dirname(__DIR__));
    }

    /**
     * @return array
     */
    public function getClassMap()
    {
        if (!empty($this->classMap)) {
            return $this->classMap;
        }

        $this->classMap = $this->classMapGenerator->generate($this->classDir);

        return $this->classMap;
    }

    /**
     * @return array
     */
    public function getServices()
    {
        if (!empty($this->services)) {
            return $this->services;
        }

        $classNames = $this->getBootstrapClassNames();

        foreach ($classNames as $className) {
            if (empty($className)) {
                continue;
            }

            if (!class_exists($className, true)) {
                $this->loadClass($className);
            }
            if (!method_exists($className, 'registerServices')) {
                continue;
            }
            $services = forward_static_call([$className, 'registerServices']);
            foreach ($services as $serviceName => $factoryMethod) {
                $this->addServiceDefinition($serviceName, $className, $factoryMethod);
            }
        }

        return $this->services;
    }

    /**
     * @throws RuntimeException
     *
     * @return SchemaCollection
     */
    public function getTableSchemas(): SchemaCollection
    {
        $schemaCollection = new SchemaCollection();
        $classNames = $this->getBootstrapClassNames();

        foreach ($classNames as $className) {
            if (empty($className)) {
                continue;
            }

            if (!class_exists($className, true)) {
                $this->loadClass($className);
            }
            if (!method_exists($className, 'registerTableSchemas')) {
                continue;
            }
            forward_static_call([$className, 'registerTableSchemas'], $schemaCollection);
        }

        return $schemaCollection;
    }

    /**
     * @return array
     */
    public function getJavascriptFiles()
    {
        $classNames = $this->getBootstrapClassNames();

        foreach ($classNames as $className) {
            if (!class_exists($className, true)) {
                continue;
            }
            if (!method_exists($className, 'registerJavascript')) {
                continue;
            }
            $javascript = forward_static_call([$className, 'registerJavascript']);
            foreach ($javascript as $cacheName => $jsFiles) {
                $this->addJavascriptDefinition($cacheName, $jsFiles);
            }
        }

        return $this->javascript;
    }

    /**
     * @return array Absolute paths to all bootstrap files
     */
    private function getBootstrapFiles()
    {
        $directory = new RecursiveDirectoryIterator($this->classDir);
        $iterator = new RecursiveIteratorIterator($directory);
        $bootstraps = new RegexIterator($iterator, '/^.+Bootstrap\.php$/', RegexIterator::MATCH);

        $files = [];

        /** @var \SplFileInfo $bootstrap */
        foreach ($bootstraps as $bootstrap) {
            $files[] = $bootstrap->getRealPath();
        }

        return $files;
    }

    /**
     * @return array FQCN of all bootstrap classes
     */
    private function getBootstrapClassNames()
    {
        if (!empty($this->bootstrapClasses)) {
            return $this->bootstrapClasses;
        }

        $files = $this->getBootstrapFiles();
        foreach ($files as $file) {
            $className = $this->classNameResolver->resolveClassName($file);
            if ($className === null) {
                continue;
            }
            $this->bootstrapClasses[] = $className;
        }

        return $this->bootstrapClasses;
    }

    /**
     * @param string $serviceName
     * @param string $bootstrapClass
     * @param string $factoryMethod
     *
     * @return void
     */
    private function addServiceDefinition($serviceName, $bootstrapClass, $factoryMethod)
    {
        if (isset($this->services[$serviceName])) {
            $registeredCallString =  $this->services[$serviceName][0] . '::' . $this->services[$serviceName][1];
            $failedCallString =  $bootstrapClass . '::' . $factoryMethod;
            throw new RuntimeException(sprintf(
                'Service "%s" can not be registered. Name is already taken. Registered "%s" - Failed "%s"',
                $serviceName, $registeredCallString, $failedCallString
            ));
        }

        $this->services[$serviceName] = [$bootstrapClass, $factoryMethod];
    }

    /**
     * @param $cacheName
     * @param $files
     *
     * @return void
     */
    private function addJavascriptDefinition($cacheName, $files)
    {
        $this->javascript[$cacheName] = $files;
    }

    /**
     * @param string $className
     *
     * @throws RuntimeException
     *
     * @return void
     */
    private function loadClass($className)
    {
        if (empty($className)) {
            return;
        }

        if (empty($this->classMap)) {
            $this->getClassMap();
        }

        if (!isset($this->classMap[$className])) {
            throw new RuntimeException(sprintf(
                'Could not load class "%s"', $className
            ));
        }

        include $this->classMap[$className];
    }
}
