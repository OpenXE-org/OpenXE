<?php

namespace Xentral\Components\Filesystem\Adapter;

use Xentral\Components\Filesystem\PathInfo;

interface ReaderAdapterInterface
{
    const TYPE_DIR = 'dir';
    const TYPE_FILE = 'file';

    /**
     * @param string $path
     *
     * @return bool
     */
    public function has($path);

    /**
     * @param string $path
     *
     * @return PathInfo|false
     */
    public function getInfo($path);

    /**
     * @param string $path
     *
     * @return string|false
     */
    public function read($path);

    /**
     * @param string $path
     *
     * @return resource|false
     */
    public function readStream($path);

    /**
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array
     */
    public function listContents($directory = '', $recursive = false);

    /**
     * @param string $path
     *
     * @return string|false [dir|file]
     */
    public function getType($path);

    /**
     * @param string $path
     *
     * @return array|false
     */
    public function getMetadata($path);

    /**
     * @param string $path
     *
     * @return int|false
     */
    public function getSize($path);

    /**
     * @param string $path
     *
     * @return int|false
     */
    public function getTimestamp($path);

    /**
     * @param string $path
     *
     * @return string|false
     */
    public function getMimetype($path);
}
