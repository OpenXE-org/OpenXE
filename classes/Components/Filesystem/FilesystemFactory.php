<?php

namespace Xentral\Components\Filesystem;

use Exception;
use Xentral\Components\Database\Database;
use Xentral\Components\Filesystem\Adapter\FtpConfig;
use Xentral\Components\Filesystem\Adapter\LeagueAdapterWrapper;
use Xentral\Components\Filesystem\Exception\FilesystemException;
use Xentral\Components\Filesystem\Exception\InvalidArgumentException;
use Xentral\Components\Filesystem\Flysystem\FtpAdapterDecorator;
use Xentral\Components\Filesystem\Flysystem\LocalAdapterDecorator;

final class FilesystemFactory
{
    /** @var Database $db */
    private $db;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * @param string $root Absolute path
     * @param array  $config
     *
     * @return FilesystemInterface
     */
    public function createLocal($root, array $config = [])
    {
        try {
            $writeFlags = isset($config['write_flags']) ? $config['write_flags'] : LOCK_EX;
            $linkHandling = isset($config['link_handling']) ? $config['link_handling'] : LocalAdapterDecorator::SKIP_LINKS;
            $permissions = isset($config['permissions']) ? $config['permissions'] : [];

            $leagueLocalAdapter = new LocalAdapterDecorator($root, $writeFlags, $linkHandling, $permissions);
            $leagueAdapterWrapper = new LeagueAdapterWrapper($leagueLocalAdapter);

            return new Filesystem($leagueAdapterWrapper);
            //
        } catch (Exception $e) {
            throw new FilesystemException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * @param FtpConfig $config
     *
     * @return FilesystemInterface
     */
    public function createFtp(FtpConfig $config)
    {
        try {
            $leagueFtpAdapter = new FtpAdapterDecorator($config->toArray());
            $leagueAdapterWrapper = new LeagueAdapterWrapper($leagueFtpAdapter);

            return new Filesystem($leagueAdapterWrapper);
            //
        } catch (Exception $e) {
            throw new FilesystemException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * @param FilesystemInterface $filesystem
     * @param int                 $syncId
     *
     * @return FilesystemSyncCache
     */
    public function createSync(FilesystemInterface $filesystem, $syncId)
    {
        try {
            if (get_class($filesystem) === FilesystemSyncCache::class) {
                throw new InvalidArgumentException('FilesystemSyncWrapper can not wrap it self.');
            }

            return new FilesystemSyncCache($this->db, $filesystem, $syncId);
            //
        } catch (Exception $e) {
            throw new FilesystemException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }
}
