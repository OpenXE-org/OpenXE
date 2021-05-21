<?php

namespace Xentral\Components\Filesystem\Adapter;

interface AdapterInterface extends ReaderAdapterInterface
{
    /**
     * Creates a new file
     *
     * @param string $path
     * @param string $contents
     * @param array  $config
     *
     * @return bool
     */
    public function write($path, $contents, array $config = []);

    /**
     * Creates a new file using a stream
     *
     * @param string   $path
     * @param resource $resource
     * @param array    $config
     *
     * @return bool
     */
    public function writeStream($path, $resource, array $config = []);

    /**
     * Updates an existing file
     *
     * @param string $path
     * @param string $contents
     * @param array  $config
     *
     * @return bool
     */
    public function update($path, $contents, array $config = []);

    /**
     * Updates an existing file using a stream
     *
     * @param string   $path
     * @param resource $resource
     * @param array    $config
     *
     * @return bool
     */
    public function updateStream($path, $resource, array $config = []);

    /**
     * Renames a file
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function rename($path, $newpath);

    /**
     * Copies a file to new location
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function copy($path, $newpath);

    /**
     * Deletes a single file
     *
     * @param string $path
     *
     * @return bool
     */
    public function delete($path);

    /**
     * Deletes a directory and its contents
     *
     * @param string $directory
     *
     * @return bool
     */
    public function deleteDir($directory);

    /**
     * @param string $directory
     * @param array  $config
     *
     * @return bool
     */
    public function createDir($directory, array $config = []);
}
