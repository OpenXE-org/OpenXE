<?php

namespace Xentral\Components\Filesystem;

use Xentral\Components\Database\Database;
use Xentral\Components\Filesystem\Exception\FileNotFoundException;
use Xentral\Components\Filesystem\Exception\FilesystemException;
use Xentral\Components\Filesystem\Exception\InvalidArgumentException;

/**
 * @todo Datenbank anlegen
 */
final class FilesystemSyncCache implements FilesystemInterface
{
    /** @var Database $db */
    private $db;

    /** @var FilesystemInterface $fs */
    private $fs;

    /** @var int $syncId */
    private $syncId;

    /**
     * @param Database            $database
     * @param FilesystemInterface $filesystem
     * @param int                 $syncId
     */
    public function __construct(Database $database, FilesystemInterface $filesystem, $syncId)
    {
        if ((int)$syncId <= 0) {
            throw new InvalidArgumentException(sprintf('Sync-ID "%s" is invalid.', $syncId));
        }

        $this->db = $database;
        $this->fs = $filesystem;
        $this->syncId = (int)$syncId;
    }

    /**
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array|PathInfo[]
     */
    public function listChanges($directory = '', $recursive = false)
    {
        $result = $this->fs->listContents($directory, $recursive);

        $location = PathUtil::normalizePath($directory);
        $cache = $this->readCache($location, $recursive);

        foreach ($result as $item) {
            $path = $item->getPath();

            // Ignore directories; only files are nessesary for syncing
            if ($item->isDir()) {
                continue;
            }

            // Defaults
            $item->set('missing', false);
            $item->set('modified', null);

            if (!array_key_exists($path, $cache)) {
                $item->set('missing', true);
                continue;
            }

            if ((int)$cache[$path]['size'] !== (int)$item->getSize()) {
                $item->set('modified', true);
                continue;
            }

            if ((int)$cache[$path]['timestamp'] !== (int)$item->getTimestamp()) {
                $item->set('modified', true);
                continue;
            }

            $item->set('modified', false);
        }

        return $result;
    }

    /**
     * Lists deleted files
     *
     * Lists files that are present in cache but does not exist on the filesystem any more.
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array|PathInfo[]
     */
    public function listDeleted($directory = '', $recursive = false)
    {
        $paths = $this->fs->listPaths($directory, $recursive);

        $location = PathUtil::normalizePath($directory);
        $cache = $this->readCache($location, $recursive);

        foreach ($cache as $path => $cacheItem) {
            if (in_array($path, $paths, true)) {
                unset($cache[$path]);
            }
        }

        $result = [];
        foreach ($cache as $cacheItem) {
            $pathinfo = PathUtil::pathinfo($cacheItem['path']);
            $result[] = new PathInfo(array_merge($cacheItem, $pathinfo));
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function has($path)
    {
        return $this->fs->has($path);
    }

    /**
     * @inheritdoc
     */
    public function getInfo($path)
    {
        return $this->fs->getInfo($path);
    }

    /**
     * @inheritdoc
     */
    public function getType($path)
    {
        return $this->fs->getType($path);
    }

    /**
     * @inheritdoc
     */
    public function getSize($path)
    {
        return $this->fs->getSize($path);
    }

    /**
     * @inheritdoc
     */
    public function getTimestamp($path)
    {
        return $this->fs->getTimestamp($path);
    }

    /**
     * @inheritdoc
     */
    public function getMimetype($path)
    {
        return $this->fs->getMimetype($path);
    }

    /**
     * @inheritdoc
     */
    public function listContents($directory = '', $recursive = false)
    {
        return $this->fs->listContents($directory, $recursive);
    }


    /**
     * @inheritdoc
     */
    public function listDirs($directory = '', $recursive = false)
    {
        return $this->fs->listDirs($directory, $recursive);
    }


    /**
     * @inheritdoc
     */
    public function listFiles($directory = '', $recursive = false)
    {
        return $this->fs->listFiles($directory, $recursive);
    }


    /**
     * @inheritdoc
     */
    public function listPaths($directory = '', $recursive = false)
    {
        return $this->fs->listPaths($directory, $recursive);
    }

    /**
     * @inheritdoc
     */
    public function read($path)
    {
        $result = $this->fs->read($path);
        $this->updateCachePath($path);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function readStream($path)
    {
        $result = $this->fs->readStream($path);
        $this->updateCachePath($path);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function write($path, $contents, array $config = [])
    {
        $result = $this->fs->write($path, $contents, $config);
        $this->updateCachePath($path);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function writeStream($path, $resource, array $config = [])
    {
        $result = $this->fs->writeStream($path, $resource, $config);
        $this->updateCachePath($path);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function put($path, $contents, array $config = [])
    {
        $result = $this->fs->put($path, $contents, $config);
        $this->updateCachePath($path);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function putStream($path, $resource, array $config = [])
    {
        $result = $this->fs->putStream($path, $resource, $config);
        $this->updateCachePath($path);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function delete($path)
    {
        $result = $this->fs->delete($path);
        $this->deleteCachePath($path);

        return $result;
    }

    /**
     * Deletes a single file, but without Exception if path does not exist.
     *
     * @param string $path
     *
     * @return bool
     */
    public function softDelete($path)
    {
        $result = false;
        try {
            $this->deleteCachePath($path);
            $result = $this->fs->delete($path);
        } catch (FileNotFoundException $e) {
            // nope - its soft
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function deleteDir($dirname)
    {
        $result = $this->fs->deleteDir($dirname);
        $this->deleteCacheDir($dirname);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function createDir($dirname, array $config = [])
    {
        return $this->fs->createDir($dirname, $config);
    }

    /**
     * @inheritdoc
     */
    public function rename($path, $newpath)
    {
        $result = $this->fs->rename($path, $newpath);
        $this->deleteCachePath($path);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function copy($path, $newpath)
    {
        $result = $this->fs->copy($path, $newpath);
        $this->updateCachePath($newpath);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getAdapter()
    {
        return $this->fs->getAdapter();
    }

    /**
     * @param string $path
     * @param bool   $recursive
     *
     * @return array
     */
    private function readCache($path, $recursive = false)
    {
        $this->ensureDependencies();
        $path = $this->normalizePath($path);

        return $this->db->fetchAssoc(
            'SELECT f.path, f.dirname, f.type, f.size, f.updated_at AS timestamp ' .
            'FROM sync_files AS f ' .
            'WHERE f.sync_id = :sync_id AND f.dirname LIKE :path_prefix',
            [
                'sync_id'     => $this->syncId,
                'path_prefix' => $recursive === true ? $path . '%' : $path,
            ]
        );
    }

    /**
     * @param string $path
     *
     * @return void
     */
    private function updateCachePath($path)
    {
        $this->ensureDependencies();
        $info = $this->fs->getInfo($path);

        $this->db->perform(
            'REPLACE INTO sync_files (sync_id, `path`, dirname, type, size, updated_at) ' .
            'VALUES (:sync_id, :path, :dirname, :type, :size, :updated_at)',
            [
                'sync_id'    => $this->syncId,
                'path'       => $info->getPath(),
                'dirname'    => $info->getDir(),
                'type'       => $info->getType(),
                'size'       => (int)$info->getSize(),
                'updated_at' => (int)$info->getTimestamp(),
            ]
        );
    }

    /**
     * @param string $path
     *
     * @return int Deleted row count
     */
    private function deleteCachePath($path)
    {
        $this->ensureDependencies();
        $path = $this->normalizePath($path);

        return $this->db->fetchAffected(
            'DELETE FROM sync_files WHERE sync_id = :sync_id AND `path` = :path',
            ['sync_id' => $this->syncId, 'path' => $path]
        );
    }

    /**
     * @param string $dirname
     *
     * @return int Deleted row count
     */
    private function deleteCacheDir($dirname)
    {
        $this->ensureDependencies();
        $dirname = $this->normalizePath($dirname);

        return $this->db->fetchAffected(
            'DELETE FROM sync_files WHERE sync_id = :sync_id AND `dirname` LIKE :dirname',
            ['sync_id' => $this->syncId, 'dirname' => $dirname . '%']
        );
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function normalizePath($path)
    {
        return PathUtil::normalizePath($path);
    }

    /**
     * @throws FilesystemException
     *
     * @return void
     */
    private function ensureDependencies()
    {
        if ($this->db === null || $this->syncId === null) {
            throw new FilesystemException('Can not continue. Required dependencies are missing.');
        }
    }
}
