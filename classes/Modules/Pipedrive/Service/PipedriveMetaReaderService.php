<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Service;

use Xentral\Modules\Pipedrive\Exception\PipedriveExceptionInterface;
use Xentral\Modules\Pipedrive\Exception\PipedriveMetaException;

final class PipedriveMetaReaderService
{
    /** @var string $tmpDir directory to save the meta file */
    private $tmpDir;

    /**
     * @param string $tmpDir
     */
    public function __construct(string $tmpDir)
    {
        $this->tmpDir = $tmpDir;
    }

    /**
     * @param string $fileName
     *
     * @throws PipedriveMetaException
     *
     * @return array|null
     */
    public function readFromFile(string $fileName): ?array
    {
        if (empty($fileName)) {
            throw new PipedriveMetaException(
                sprintf('::readFromFile() Expects Meta content to be non empty string file, %s given', $fileName)
            );
        }

        if (!$this->exists($fileName)) {
            return null;
        }

        $fullFileName = $this->getFullFileName($fileName);

        $meta = @file_get_contents($fullFileName);

        if (($meta = json_decode($meta, true)) === null || (json_last_error() !== JSON_ERROR_NONE)) {
            return [];
        }

        return $meta;
    }

    /**
     * @param string $fileName
     *
     * @throws PipedriveMetaException
     *
     * @return string|null
     */
    private function getFullFileName(string $fileName): ?string
    {
        $metaFile = sprintf($this->tmpDir . DIRECTORY_SEPARATOR . '%s', $fileName);
        if (!is_file($metaFile)) {
            throw new PipedriveMetaException(sprintf('File "%s" was not found', $metaFile));
        }

        return $metaFile;
    }

    /**
     * @param string $fileName
     *
     * @return bool
     */
    public function exists(string $fileName): bool
    {
        try {
            $fullFileName = $this->getFullFileName($fileName);

            return file_exists($fullFileName) && is_file($fullFileName);
        } catch (PipedriveExceptionInterface $exception) {
            //
        }

        return false;
    }

    /**
     * @param string $key
     * @param string $fileName
     *
     * @throws PipedriveMetaException
     *
     * @return bool
     */
    public function hasKey(string $key, string $fileName): bool
    {
        return $this->exists($fileName) && ($meta = $this->readFromFile($fileName)) && array_key_exists($key, $meta);
    }
}
