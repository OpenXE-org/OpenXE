<?php

namespace Xentral\Core\Installer;

use RuntimeException;

final class InstallerCacheWriter
{
    /** @var InstallerCacheConfig $config */
    private $config;

    /** @var Installer $installer */
    private $installer;

    /**
     * @param InstallerCacheConfig $config
     * @param Installer            $installer
     */
    public function __construct(InstallerCacheConfig $config, Installer $installer)
    {
        $this->config = $config;
        $this->installer = $installer;
    }

    /**
     * @internal Wird momentan nicht verwendet, da inkompatibel mit Ioncube
     *
     * @param string|null $cacheFile Absolute path to file
     *
     * @return void
     */
    public function writeClassMap($cacheFile = null)
    {
        if ($cacheFile === null) {
            $cacheFile = $this->config->getClassMapCacheFile();
        }

        $classMap = $this->installer->getClassMap();

        $lines = [];
        $lines[] = '<?php';
        $lines[] = '';
        $lines[] = 'return array(';
        foreach ($classMap as $class => $file) {
            $lines[] .= sprintf('    %s => %s,', var_export($class, true), var_export($file, true));
        }
        $lines[] = ');';

        $contents = '';
        foreach ($lines as $line) {
            $contents .= $line . "\n";
        }

        $this->writeCacheFile($cacheFile, $contents);
    }

    /**
     * @param string|null $cacheFile Absolute path to file
     *
     * @return void
     */
    public function writeServiceCache($cacheFile = null)
    {
        if ($cacheFile === null) {
            $cacheFile = $this->config->getServiceCacheFile();
        }

        $serviceFactories = $this->installer->getServices();
        $content = "<?php \n\nreturn array(\n";

        foreach ($serviceFactories as $service => $callable) {
            $content .= sprintf(
                "    %s => array(%s, %s),\n",
                var_export($service, true),
                var_export($callable[0], true),
                var_export($callable[1], true)
            );
        }

        $content .= ");\n";

        $this->writeCacheFile($cacheFile, $content);
    }

    /**
     * @param string|null $cacheFile Absolute path to file
     *
     * @return void
     */
    public function writeJavascriptCache($cacheFile = null)
    {
        if ($cacheFile === null) {
            $cacheFile = $this->config->getJavascriptCacheFile();
        }

        $javascript = $this->installer->getJavascriptFiles();
        $content = "<?php \n\nreturn " . var_export($javascript, true) . ";\n";

        $this->writeCacheFile($cacheFile, $content);
    }

    /**
     * @param string $cacheFile
     * @param string $contents
     *
     * @throws RuntimeException
     *
     * @return void
     */
    private function writeCacheFile($cacheFile, $contents)
    {
        if (!@file_put_contents($cacheFile, $contents)) {
            throw new RuntimeException(sprintf(
                'Cache-Datei "%s" konnte nicht erzeugt werden. Vermutlich fehlen Schreibrechte in %s',
                $cacheFile, $this->config->getUserDataTempDir()
            ));
        }
    }
}
