<?php

namespace Xentral\Core\Installer;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

/**
 * Scans recursively a directory and generates a class map for autoloading
 */
final class ClassMapGenerator
{
    /** @var Psr4ClassNameResolver $resolver */
    private $resolver;

    /** @var string $baseDir */
    private $baseDir;

    /**
     * @param Psr4ClassNameResolver $resolver
     * @param string                $baseDir Absolute path to installation folder
     */
    public function __construct(Psr4ClassNameResolver $resolver, $baseDir)
    {
        $this->resolver = $resolver;
        $this->baseDir = $this->removeTrailingSlashFromDirectory($baseDir);
    }

    /**
     * @param string $scanDir Absolute path to directory
     *
     * @return array
     */
    public function generate($scanDir)
    {
        if (!is_dir($scanDir)) {
            throw new \RuntimeException(sprintf(
                '"%s" is not a directory.', $scanDir
            ));
        }

        return $this->scanDir($scanDir);
        //return $this->prepareClassMap($classMap);
    }

    /**
     * @param string $scanDir Absolute path (without trailing slash)
     *
     * @return array
     */
    private function scanDir($scanDir)
    {
        $scanDir = $this->removeTrailingSlashFromDirectory($scanDir);

        $directory = new RecursiveDirectoryIterator($scanDir);
        $iterator = new RecursiveIteratorIterator($directory);
        $matcher = new RegexIterator($iterator, '/^.+\.php$/', RegexIterator::MATCH);

        $files = [];

        /** @var \SplFileInfo $match */
        foreach ($matcher as $match) {
            $files[] = $match->getRealPath();
        }

        $map = [];
        foreach ($files as $file) {
            $className = $this->resolver->resolveClassName($file);
            if ($className !== null) {
                $map[$className] = $file;
            }
        }

        return $map;
    }

    /**
     * Prepare file paths; make them relative to base dir
     *
     * @param array $classMap
     *
     * @return array
     */
    private function prepareClassMap(array $classMap)
    {
        $prepared = [];

        foreach ($classMap as $class => $file) {
            $relativePath = str_replace($this->baseDir, '', $file);
            $prepared[$class] = $relativePath;
        }

        return $prepared;
    }

    /**
     * @param string $dir
     *
     * @return string
     */
    private function removeTrailingSlashFromDirectory($dir)
    {
        if (substr($dir, -1) === '/') {
            return substr_replace($dir, '', -1);
        }

        return $dir;
    }
}
