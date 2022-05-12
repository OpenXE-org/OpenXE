<?php

namespace Xentral\Components\Filesystem;

use Xentral\Components\Filesystem\Adapter\AdapterInterface;
use Xentral\Components\Filesystem\Exception\DirNotFoundException;
use Xentral\Components\Filesystem\Exception\FileExistsException;
use Xentral\Components\Filesystem\Exception\FileNotFoundException;
use Xentral\Components\Filesystem\Exception\InvalidArgumentException;
use Xentral\Components\Filesystem\Exception\RootViolationException;

interface FilesystemInterface
{
    /**
     * Checks if file or directory exists
     *
     * @param string $path
     *
     * @return bool
     */
    public function has($path);

    /**
     * @param string $path
     *
     * @throws FileNotFoundException
     *
     * @return PathInfo|false
     */
    public function getInfo($path);

    /**
     * List directory contents
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array|PathInfo[]
     */
    public function listContents($directory = '', $recursive = false);

//    public function filterContents(array $filter = [], $directory = '', $recursive = false); // @todo
//
//    // @todo ExtendedLocal
//    public function isReadable();
//    public function isWriteable();
//    public function getOwner();
//    public function getGroup();

    /**
     * Lists only directories
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array|PathInfo[]
     */
    public function listDirs($directory = '', $recursive = false);

    /**
     * Lists only files
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array|PathInfo[]
     */
    public function listFiles($directory = '', $recursive = false);

    /**
     * List only paths as strings
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array|string[]
     */
    public function listPaths($directory = '', $recursive = false);

    /**
     * @param string $path
     *
     * @throws FileNotFoundException
     *
     * @return string [dir|file]
     */
    public function getType($path);

    /**
     * Gets the filesize
     *
     * @param string $path
     *
     * @throws FileNotFoundException
     *
     * @return int|false
     */
    public function getSize($path);

    /**
     * Gets the timestamp from last update
     *
     * @param string $path
     *
     * @throws FileNotFoundException
     *
     * @return string|false
     */
    public function getTimestamp($path);

    /**
     * @param string $path
     *
     * @throws FileNotFoundException
     *
     * @return string|false
     */
    public function getMimetype($path);

    /**
     * Read file content
     *
     * @param string $path
     *
     * @throws FileNotFoundException
     *
     * @return string|false
     */
    public function read($path);

    /**
     * Read file content
     *
     * @param string $path
     *
     * @throws FileNotFoundException
     *
     * @return resource|false
     */
    public function readStream($path);

    /**
     * Writes to a new file
     *
     * @param string $path
     * @param string $contents
     * @param array  $config
     *
     * @throws FileExistsException
     *
     * @return bool
     */
    public function write($path, $contents, array $config = []);

    /**
     * Writes to a new file
     *
     * @param string   $path
     * @param resource $resource
     * @param array    $config
     *
     * @throws InvalidArgumentException
     * @throws FileExistsException
     *
     * @return bool
     */
    public function writeStream($path, $resource, array $config = []);

    /**
     * Creates a file or updates the file contents
     *
     * @param string $path
     * @param string $contents
     * @param array  $config
     *
     * @return bool
     */
    public function put($path, $contents, array $config = []);

    /**
     * Creates a file or updates the file contents
     *
     * @param string   $path
     * @param resource $resource
     * @param array    $config
     *
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    public function putStream($path, $resource, array $config = []);

    /**
     * Renames a file
     *
     * @param string $path
     * @param string $newpath
     *
     * @throws FileExistsException
     * @throws FileNotFoundException
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
     * @throws FileExistsException
     * @throws FileNotFoundException
     *
     * @return bool
     */
    public function copy($path, $newpath);

    /**
     * Deletes a single file
     *
     * @param string $path
     *
     * @throws FileNotFoundException
     *
     * @return bool
     */
    public function delete($path);

    /**
     * Deletes a directory and all its contents
     *
     * @param string $dirname
     *
     * @throws DirNotFoundException
     * @throws RootViolationException
     *
     * @return bool
     */
    public function deleteDir($dirname);

    /**
     * Creates a directory
     *
     * @param string $dirname
     * @param array  $config
     *
     * @return bool
     */
    public function createDir($dirname, array $config = []);

    /**
     * @return AdapterInterface
     */
    public function getAdapter();
}
