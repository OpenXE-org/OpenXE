<?php

namespace Xentral\Components\Filesystem\Plugin;

use Xentral\Components\Filesystem\FilesystemInterface;

interface PluginInterface
{
    /**
     * @return string
     */
    public function getMethod();

    /**
     * @param FilesystemInterface $filesystem
     */
    public function setFilesystem(FilesystemInterface $filesystem);
}
