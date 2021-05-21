<?php

namespace Xentral\Core\Installer;

use RuntimeException;

final class InstallerCacheConfig
{
    /** @var string $userdataDir */
    private $userdataTempDir;

    /**
     * @param string $userdataTempDir
     */
    public function __construct($userdataTempDir)
    {
        $this->userdataTempDir = $userdataTempDir;
        if (!is_dir($userdataTempDir)) {
            $this->createUserDataTempDir();
        }
    }

    /**
     * @return string
     */
    public function getUserDataTempDir()
    {
        return $this->userdataTempDir;
    }

    /**
     * @return string
     */
    public function getClassMapCacheFile()
    {
        return $this->userdataTempDir . '/cache_classmap.php';
    }

    /**
     * @return string
     */
    public function getServiceCacheFile()
    {
        return $this->userdataTempDir . '/cache_services.php';
    }

    /**
     * @return string
     */
    public function getJavascriptCacheFile()
    {
        return $this->userdataTempDir . '/cache_javascript.php';
    }

    /**
     * @return void
     */
    private function createUserDataTempDir(): void
    {
        if (!mkdir($this->userdataTempDir, 0777, true) && !is_dir($this->userdataTempDir)) {
            throw new RuntimeException(sprintf(
                'Verzeichnis "%s" konnte nicht angelegt werden.', $this->userdataTempDir
            ));
        }
    }
}
