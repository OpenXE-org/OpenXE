<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm;

use Config;
use Xentral\Components\Filesystem\FilesystemFactory;
use Xentral\Components\Filesystem\FilesystemInterface;
use Xentral\Core\LegacyConfig\ConfigLoader;
use Xentral\Modules\Datanorm\Handler\DatanormReaderHandlerInterface;
use Xentral\Modules\Datanorm\Service\DatanormIntermediateService;
use Xentral\Modules\Datanorm\Service\DatanormReader;

final class DatanormReaderFactory
{
    /** @var DatanormIntermediateService $intermediateService */
    private $intermediateService;

    /** @var DatanormReaderHandlerInterface[] $readerHandlers */
    private $readerHandlers;

    /** @var FilesystemFactory $fileSystemFactory */
    private $fileSystemFactory;

    /**
     * @param DatanormIntermediateService $intermediateService
     * @param array                       $readerHandlers
     * @param FilesystemFactory           $fileSystemFactory
     */
    public function __construct(
        DatanormIntermediateService $intermediateService,
        array $readerHandlers,
        FilesystemFactory $fileSystemFactory
    ) {
        $this->intermediateService = $intermediateService;
        $this->readerHandlers = $readerHandlers;
        $this->fileSystemFactory = $fileSystemFactory;
    }

    /**
     * @param string $uploadDir
     * @param string $baseDir
     *
     * @return DatanormReader
     */
    public function createDatanormReader(string $uploadDir, string $baseDir = ''): DatanormReader
    {
        return new DatanormReader(
            $this->getFileSystem($baseDir),
            $this->intermediateService,
            $this->readerHandlers,
            $uploadDir
        );
    }

    /**
     * @param string $baseDir
     *
     * @return FilesystemInterface
     */
    private function getFileSystem(string $baseDir = ''): FilesystemInterface
    {
        $config = ConfigLoader::load();

        if (empty($baseDir)) {
            $baseDir = $config->WFuserdata !== null
                ? $config->WFuserdata
                : dirname(dirname(dirname(__DIR__))) . '/userdata';
        }

        $fileSystemConfig = [
            'permissions' => [
                'file' => [
                    'public'  => 0664,
                    'private' => 0664,
                ],
                'dir'  => [
                    'public'  => 0775,
                    'private' => 0775,
                ],
            ],
        ];

        return $this->fileSystemFactory->createLocal($baseDir, $fileSystemConfig);
    }
}
