<?php

namespace Xentral\Core\Installer;

/**
 * Resolves full-qualified class names (PSR-4) by file path
 */
final class Psr4ClassNameResolver
{
    /** @var array $prefixes Registered namespace prefixes */
    private $prefixes = [];

    /** @var array $excludes Excludes files */
    private $excludes = [];

    /**
     * @param array $prefixes
     */
    public function __construct(array $prefixes = [])
    {
        foreach ($prefixes as $prefix => $fileDir) {
            $this->addNamespace($prefix, $fileDir);
        }
    }

    /**
     * @example addNamespace('App\\', '/path/to/src')
     *
     * @param string $prefix  Namespace prefix, e.g. App\
     * @param string $baseDir Absolute path to directory
     *
     * @return void
     */
    public function addNamespace($prefix, $baseDir)
    {
        // Normalize inputs
        $prefix = trim($prefix, '\\') . '\\';
        $baseDir = rtrim($baseDir, '/') . '/';

        $this->prefixes[$prefix] = $baseDir;
    }

    /**
     * @param string $filePath Absolute path to file
     *
     * @return void
     */
    public function excludeFile($filePath)
    {
        $this->excludes[] = $filePath;
    }

    /**
     * @param string $filePath Absolute path to class file
     *
     * @return string|null Full-qualified class name
     */
    public function resolveClassName($filePath)
    {
        // .src.php are built by the Build-Server and are not needed for execution
        if (strpos($filePath, '.src.php') !== false) {
            return null;
        }
        if (in_array($filePath, $this->excludes, true)) {
            return null;
        }

        foreach ($this->prefixes as $prefix => $baseDir) {
            if (strpos($filePath, $baseDir) === 0) {
                $offset = strlen($baseDir);
                $relativePath = substr($filePath, $offset);
                $relativePath = str_ireplace('.php', '', $relativePath);
                $relativeNamespace = str_replace('/', '\\', $relativePath);

                return $prefix . $relativeNamespace;
            }
        }

        return null;
    }
}
