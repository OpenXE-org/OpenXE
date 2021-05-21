<?php

namespace Xentral\Components\Filesystem\Plugin;

use Xentral\Components\Filesystem\FilesystemInterface;

final class FilterPathsPlugin implements PluginInterface
{
    /** @var FilesystemInterface $filesystem */
    private $filesystem;

    /**
     * @return string
     */
    public function getMethod()
    {
        return 'filterPaths';
    }

    /**
     * @param FilesystemInterface $filesystem
     */
    public function setFilesystem(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @example filterPaths(['extension' => 'php', 'filename' => 'Bootstrap'])
     *
     * @param array  $filter
     * @param string $path
     * @param bool   $recursive
     *
     * @return array
     */
    public function handle(array $filter = [], $path = '', $recursive = false)
    {
        $result = [];
        $contents = $this->filesystem->listContents($path, $recursive);

        foreach ($contents as $object) {
            $matchesFilter = true;

            foreach ($filter as $property => $value) {
                if (!isset($object[$property])) {
                    $matchesFilter = false;
                    continue;
                }
                if ($object[$property] !== $value) {
                    $matchesFilter = false;
                    continue;
                }
            }

            if ($matchesFilter === true) {
                $result[] = $object['path'];
            }
        }

        return $result;
    }
}
