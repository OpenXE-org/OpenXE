<?php

namespace Xentral\Components\Exporter\Json;

use Xentral\Components\Exporter\Exception\FileExistsException;
use Xentral\Components\Exporter\Exception\InvalidResourceException;

final class JsonExporter
{
    /** @var JsonConfig $config */
    private $config;

    /**
     * @param JsonConfig|null $config
     */
    public function __construct(JsonConfig $config = null)
    {
        if ($config === null) {
            $config = new JsonConfig();
        }

        $this->config = $config;
    }

    /**
     * @param string $filePath Resource used for writing
     * @param array  $data     Multi-dimentional array
     *
     * @throws \Xentral\Components\Exporter\Exception\InvalidJsonException
     * @throws \Xentral\Components\Exporter\Exception\ResourceWriteException
     * @return void
     */
    public function export($filePath, $data)
    {
        $resource = $this->exportToResource($filePath, $data);
        fclose($resource);
    }

    /**
     * Same as ::export() beside that the created resource will be returned
     *
     * @param string $filePath Resource used for writing
     * @param array  $data     Multi-dimentional array
     *
     * @throws \Xentral\Components\Exporter\Exception\InvalidJsonException
     * @throws \Xentral\Components\Exporter\Exception\ResourceWriteException
     * @return resource
     */
    public function exportToResource($filePath, $data)
    {
        if (is_file($filePath)) {
            throw new FileExistsException(sprintf('File creation failed. File "%s" already exists.', $filePath));
        }

        // 'x+' = Create and open for reading and writing.
        //        File pointer will be placed at the beginning of the file.
        //        If the file already exists `fopen` will return false.
        // 'b'  = Enable binary mode
        $resource = @fopen($filePath, 'x+b');
        if ($resource === false) {
            throw new InvalidResourceException(sprintf('Failed to open resource for file path "%s".', $filePath));
        }

        $writer = new JsonWriter($resource, $this->config);
        $writer->write($data);

        return $resource;
    }
}
