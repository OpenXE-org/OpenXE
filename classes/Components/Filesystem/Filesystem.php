<?php

namespace Xentral\Components\Filesystem;

use Xentral\Components\Filesystem\Adapter\AdapterInterface;
use Xentral\Components\Filesystem\Exception\DirNotFoundException;
use Xentral\Components\Filesystem\Exception\FileExistsException;
use Xentral\Components\Filesystem\Exception\FileNotFoundException;
use Xentral\Components\Filesystem\Exception\InvalidArgumentException;
use Xentral\Components\Filesystem\Exception\RootViolationException;

final class Filesystem implements FilesystemInterface
{
    /** @var AdapterInterface $adapter */
    private $adapter;

    /**
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Checks if file or directory exists
     *
     * @param string $path
     *
     * @return bool
     */
    public function has($path)
    {
        return $this->adapter->has($path);
    }

    /**
     * @param string $path
     *
     * @throws FileNotFoundException
     *
     * @return PathInfo|false
     */
    public function getInfo($path)
    {
        if (!$this->has($path)) {
            throw new FileNotFoundException(sprintf('File "%s" not found.', $path));
        }

        return $this->adapter->getInfo($path);
    }

    /**
     * List directory contents
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array|PathInfo[]
     */
    public function listContents($directory = '', $recursive = false)
    {
        $result = [];

        $contents = $this->adapter->listContents($directory, $recursive);
        foreach ($contents as $metainfo) {
            $result[] = PathInfo::fromMeta($metainfo);
        }

        return $result;
    }

    /**
     * Lists only directories
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array|PathInfo[]
     */
    public function listDirs($directory = '', $recursive = false)
    {
        $result = [];

        $contents = $this->adapter->listContents($directory, $recursive);
        foreach ($contents as $metainfo) {
            if ($metainfo['type'] === 'dir') {
                $result[] = PathInfo::fromMeta($metainfo);
            }
        }

        return $result;
    }

    /**
     * Lists only files
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array|PathInfo[]
     */
    public function listFiles($directory = '', $recursive = false)
    {
        $result = [];

        $contents = $this->adapter->listContents($directory, $recursive);
        foreach ($contents as $metainfo) {
            if ($metainfo['type'] === 'file') {
                $result[] = PathInfo::fromMeta($metainfo);
            }
        }

        return $result;
    }

    /**
     * List only paths as strings
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array|string[]
     */
    public function listPaths($directory = '', $recursive = false)
    {
        $contents = $this->adapter->listContents($directory, $recursive);

        return array_column($contents, 'path');
    }

    /**
     * @param string $path
     *
     * @throws FileNotFoundException
     *
     * @return string [dir|file]
     */
    public function getType($path)
    {
        if (!$this->has($path)) {
            throw new FileNotFoundException(sprintf('File "%s" not found.', $path));
        }

        return $this->adapter->getType($path);
    }

    /**
     * Gets the filesize
     *
     * @param string $path
     *
     * @throws FileNotFoundException
     *
     * @return int|false
     */
    public function getSize($path)
    {
        if (!$this->has($path)) {
            throw new FileNotFoundException(sprintf('File "%s" not found.', $path));
        }

        return $this->adapter->getSize($path);
    }

    /**
     * Gets the timestamp from last update
     *
     * @param string $path
     *
     * @throws FileNotFoundException
     *
     * @return string|false
     */
    public function getTimestamp($path)
    {
        if (!$this->has($path)) {
            throw new FileNotFoundException(sprintf('File "%s" not found.', $path));
        }

        return $this->adapter->getTimestamp($path);
    }

    /**
     * @param string $path
     *
     * @throws FileNotFoundException
     *
     * @return string|false
     */
    public function getMimetype($path)
    {
        if (!$this->has($path)) {
            throw new FileNotFoundException(sprintf('File "%s" not found.', $path));
        }

        return $this->adapter->getMimetype($path);
    }

    /**
     * Read file content
     *
     * @param string $path
     *
     * @throws FileNotFoundException
     *
     * @return string|false
     */
    public function read($path)
    {
        if (!$this->has($path)) {
            throw new FileNotFoundException(sprintf('File "%s" not found.', $path));
        }

        return $this->adapter->read($path);
    }

    /**
     * Read file content
     *
     * @param string $path
     *
     * @throws FileNotFoundException
     *
     * @return resource|false
     */
    public function readStream($path)
    {
        if (!$this->has($path)) {
            throw new FileNotFoundException(sprintf('File "%s" not found.', $path));
        }

        return $this->adapter->readStream($path);
    }

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
    public function write($path, $contents, array $config = [])
    {
        if ($this->has($path)) {
            throw new FileExistsException(sprintf('File "%s" exists already.', $path));
        }

        return $this->adapter->write($path, $contents, $config);
    }

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
    public function writeStream($path, $resource, array $config = [])
    {
        if ($this->has($path)) {
            throw new FileExistsException(sprintf('File "%s" exists already.', $path));
        }
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('Second parameter must be a resource.');
        }

        return $this->adapter->writeStream($path, $resource, $config);
    }

    /**
     * Creates a file or updates the file contents
     *
     * @param string $path
     * @param string $contents
     * @param array  $config
     *
     * @return bool
     */
    public function put($path, $contents, array $config = [])
    {
        if (!$this->has($path)) {
            return $this->adapter->write($path, $contents, $config);
        }

        return $this->adapter->update($path, $contents, $config);
    }

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
    public function putStream($path, $resource, array $config = [])
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('Second parameter must be a resource.');
        }

        if (!$this->has($path)) {
            return $this->adapter->writeStream($path, $resource, $config);
        }

        return $this->adapter->updateStream($path, $resource, $config);
    }

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
    public function rename($path, $newpath)
    {
        if (!$this->has($path)) {
            throw new FileNotFoundException(sprintf('File "%s" not found.', $path));
        }
        if ($this->has($newpath)) {
            throw new FileExistsException(sprintf('File "%s" exists already.', $newpath));
        }

        return $this->adapter->rename($path, $newpath);
    }

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
    public function copy($path, $newpath)
    {
        if (!$this->has($path)) {
            throw new FileNotFoundException(sprintf('File "%s" not found.', $path));
        }
        if ($this->has($newpath)) {
            throw new FileExistsException(sprintf('File "%s" exists already.', $newpath));
        }

        return $this->adapter->copy($path, $newpath);
    }

    /**
     * Deletes a single file
     *
     * @param string $path
     *
     * @throws FileNotFoundException
     *
     * @return bool
     */
    public function delete($path)
    {
        if (!$this->has($path)) {
            throw new FileNotFoundException(sprintf('File "%s" not found.', $path));
        }

        return $this->adapter->delete($path);
    }

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
    public function deleteDir($dirname)
    {
        $info = $this->adapter->getInfo($dirname);
        if (!$info) {
            throw new DirNotFoundException(sprintf('Directory "%s" not found.', $dirname));
        }
        if ($info->getPath() === '') {
            throw new RootViolationException('Root directory can not be deleted.');
        }

        return $this->adapter->deleteDir($dirname);
    }

    /**
     * Creates a directory
     *
     * @param string $dirname
     * @param array  $config
     *
     * @return bool
     */
    public function createDir($dirname, array $config = [])
    {
        return $this->adapter->createDir($dirname, $config);
    }

    /**
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }
}
