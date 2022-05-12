<?php

namespace Xentral\Components\Filesystem\Adapter;

use League\Flysystem\AdapterInterface as LeagueAdapterInterface;
use League\Flysystem\Config as LeagueConfig;
use League\Flysystem\Util;
use League\Flysystem\Util\ContentListingFormatter;
use Xentral\Components\Filesystem\PathInfo;

final class LeagueAdapterWrapper implements AdapterInterface
{
    /** @var LeagueAdapterInterface $league */
    private $league;

    /** @var bool $caseSensitive */
    private $caseSensitive = true;

    /**
     * @param LeagueAdapterInterface $league
     */
    public function __construct(LeagueAdapterInterface $league)
    {
        $this->league = $league;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function has($path)
    {
        $path = $this->normalizePath($path);

        return (bool)$this->league->has($path) !== false;
    }

    /**
     * @param string $path
     *
     * @return PathInfo|false
     */
    public function getInfo($path)
    {
        $path = $this->normalizePath($path);
        $metainfo = $this->league->getMetadata($path);
        if (!$metainfo) {
            return false;
        }

        $directory = Util::dirname($path);
        $metainfo['path'] = $path;

        $formatter = new ContentListingFormatter($directory, false, $this->caseSensitive);
        $contents = $formatter->formatListing([$metainfo]);
        if (count($contents) !== 1) {
            return false;
        }

        return new PathInfo($contents[0]);
    }

    /**
     * @param string $path
     *
     * @return string|false
     */
    public function read($path)
    {
        $path = $this->normalizePath($path);
        $meta = $this->league->getMetadata($path);
        if ($meta['type'] === self::TYPE_DIR) {
            return false;
        }


        $result = $this->league->read($path);
        if (!$result || !isset($result['contents'])) {
            return false;
        }

        return $result['contents'];
    }

    /**
     * @param string $path
     *
     * @return resource|false
     */
    public function readStream($path)
    {
        $path = $this->normalizePath($path);
        $meta = $this->league->getMetadata($path);
        if ($meta['type'] === self::TYPE_DIR) {
            return false;
        }

        $result = $this->league->readStream($path);
        if (!$result || !isset($result['stream'])) {
            return false;
        }

        return $result['stream'];
    }

    /**
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array
     */
    public function listContents($directory = '', $recursive = false)
    {
        $directory = $this->normalizePath($directory);
        $contents = $this->getLeagueAdapter()->listContents($directory, $recursive);
        $formatter = new ContentListingFormatter($directory, $recursive, $this->caseSensitive);

        return $formatter->formatListing($contents);
    }

    /**
     * @param string $path
     *
     * @return array|false
     */
    public function getMetadata($path)
    {
        $path = $this->normalizePath($path);

        return $this->league->getMetadata($path);
    }

    /**
     * @param string $path
     *
     * @return string|false
     */
    public function getType($path)
    {
        $path = $this->normalizePath($path);
        $meta = $this->league->getMetadata($path);
        if ($meta === false || !isset($meta['type'])) {
            return false;
        }

        return $meta['type'];
    }

    /**
     * @param string $path
     *
     * @return int|false
     */
    public function getSize($path)
    {
        $path = $this->normalizePath($path);
        $meta = $this->league->getSize($path);
        if ($meta === false || !isset($meta['size'])) {
            return false;
        }

        return (int)$meta['size'];
    }

    /**
     * @param string $path
     *
     * @return int|false
     */
    public function getTimestamp($path)
    {
        $path = $this->normalizePath($path);
        $meta = $this->league->getTimestamp($path);
        if ($meta === false || !isset($meta['timestamp'])) {
            return false;
        }

        return (int)$meta['timestamp'];
    }

    /**
     * @param string $path
     *
     * @return string|false
     */
    public function getMimetype($path)
    {
        $path = $this->normalizePath($path);
        $meta = $this->league->getMimetype($path);
        if ($meta === false || !isset($meta['mimetype'])) {
            return false;
        }
        if ($meta['type'] === 'dir') {
            return 'directory';
        }

        return $meta['mimetype'];
    }

    /**
     * Creates a new file
     *
     * @param string $path
     * @param string $contents
     * @param array  $config
     *
     * @return bool
     */
    public function write($path, $contents, array $config = [])
    {
        $path = $this->normalizePath($path);

        return $this->league->write($path, $contents, new LeagueConfig($config)) !== false;
    }

    /**
     * Creates a new file using a stream
     *
     * @param string   $path
     * @param resource $resource
     * @param array    $config
     *
     * @return bool
     */
    public function writeStream($path, $resource, array $config = [])
    {
        $path = $this->normalizePath($path);

        return $this->league->writeStream($path, $resource, new LeagueConfig($config)) !== false;
    }

    /**
     * Updates an existing file
     *
     * @param string $path
     * @param string $contents
     * @param array  $config
     *
     * @return bool
     */
    public function update($path, $contents, array $config = [])
    {
        $path = $this->normalizePath($path);

        return $this->league->update($path, $contents, new LeagueConfig($config)) !== false;
    }

    /**
     * Updates an existing file using a stream
     *
     * @param string   $path
     * @param resource $resource
     * @param array    $config
     *
     * @return bool
     */
    public function updateStream($path, $resource, array $config = [])
    {
        $path = $this->normalizePath($path);

        return $this->league->updateStream($path, $resource, new LeagueConfig($config)) !== false;
    }

    /**
     * Renames a file
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function rename($path, $newpath)
    {
        $path = $this->normalizePath($path);
        $newpath = $this->normalizePath($newpath);

        return $this->league->rename($path, $newpath);
    }

    /**
     * Copies a file to new location
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function copy($path, $newpath)
    {
        $path = $this->normalizePath($path);
        $newpath = $this->normalizePath($newpath);

        return $this->league->copy($path, $newpath);
    }

    /**
     * Deletes a single file
     *
     * @param string $path
     *
     * @return bool
     */
    public function delete($path)
    {
        $path = $this->normalizePath($path);

        return $this->league->delete($path);
    }

    /**
     * Deletes a directory and its contents
     *
     * @param string $directory
     *
     * @return bool
     */
    public function deleteDir($directory)
    {
        $directory = $this->normalizePath($directory);

        return $this->league->deleteDir($directory);
    }

    /**
     * @param string $directory
     * @param array  $config
     *
     * @return bool
     */
    public function createDir($directory, array $config = [])
    {
        $directory = $this->normalizePath($directory);

        return $this->league->createDir($directory, new LeagueConfig($config)) !== false;
    }

    /**
     * @return LeagueAdapterInterface
     */
    public function getLeagueAdapter()
    {
        return $this->league;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function normalizePath($path)
    {
        return Util::normalizePath($path);
    }
}
