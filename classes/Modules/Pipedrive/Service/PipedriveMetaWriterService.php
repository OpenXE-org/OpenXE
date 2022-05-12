<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Service;

use Xentral\Modules\Pipedrive\Exception\PipedriveMetaException;

final class PipedriveMetaWriterService
{
    /** @var string $tmpDir directory to save the meta file */
    private $tmpDir;

    /**
     * PipedriveMetaWriterService constructor.
     *
     * @param string $tmpDir
     */
    public function __construct(string $tmpDir)
    {
        $this->tmpDir = $tmpDir;
    }

    /**
     * @param string $fileName
     * @param array  $data
     *
     * @throws PipedriveMetaException
     *
     * @return false|int
     */
    public function save(string $fileName, array $data)
    {
        if (empty($fileName)) {
            throw new PipedriveMetaException('Name cannot be empty');
        }

        if (!function_exists('json_encode')) {
            throw new PipedriveMetaException('Required PHP extension "json" is missing.');
        }

        $content = json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

        return file_put_contents(
            $this->getFullFileName($fileName),
            $content
        );
    }

    /**
     * @param string $fileName
     *
     * @throws PipedriveMetaException
     *
     * @return string
     */
    private function getFullFileName(string $fileName): string
    {
        if (!is_dir($this->tmpDir) && !@mkdir($this->tmpDir, 0777, true) && !is_dir($this->tmpDir)) {
            throw new PipedriveMetaException(sprintf('Directory "%s" was not created', $this->tmpDir));
        }

        return sprintf($this->tmpDir . DIRECTORY_SEPARATOR . '%s', $fileName);
    }

    /**
     * @param string $fileName
     *
     * @throws PipedriveMetaException
     *
     * @return bool
     */
    public function delete(string $fileName): bool
    {
        $metaFile = $this->getFullFileName($fileName);

        return file_exists($metaFile) && is_file($metaFile) && @unlink($metaFile);
    }
}
