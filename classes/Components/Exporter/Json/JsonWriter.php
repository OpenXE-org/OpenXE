<?php

namespace Xentral\Components\Exporter\Json;

use JsonSerializable;
use Xentral\Components\Exporter\Exception\InvalidJsonException;
use Xentral\Components\Exporter\Exception\InvalidResourceException;
use Xentral\Components\Exporter\Exception\PhpExtensionMissingException;
use Xentral\Components\Exporter\Exception\ResourceWriteException;

final class JsonWriter
{
    /** @var resource $handle */
    private $handle;

    /** @var JsonConfig $config */
    private $config;

    /**
     * @param resource        $handle
     * @param JsonConfig|null $config
     *
     * @throws InvalidResourceException If resource is not writable or invalid
     * @throws PhpExtensionMissingException If mbstring is missing
     */
    public function __construct($handle, JsonConfig $config = null)
    {
        if (!is_resource($handle)) {
            throw new InvalidResourceException('First parameter is not a valid resource.');
        }
        if (!$this->isStreamWritable($handle)) {
            throw new InvalidResourceException('Resource is not writable.');
        }
        if (!function_exists('mb_convert_encoding')) {
            throw new PhpExtensionMissingException('Required PHP extension "mbstring" is missing.');
        }
        if ($config === null) {
            $config = new JsonConfig();
        }

        $this->config = $config;
        $this->handle = $handle;
    }

    /**
     * @param array|JsonSerializable $data
     *
     * @throws PhpExtensionMissingException If json is missing
     * @throws ResourceWriteException
     * @throws InvalidJsonException
     */
    public function write($data)
    {
        if (!function_exists('json_encode')) {
            throw new PhpExtensionMissingException('Required PHP extension "json" is missing.');
        }

        $jsonOptions = $this->config->getOptions();
        $jsonString = @json_encode($data, $jsonOptions);

        if ($jsonString === false) {
            throw InvalidJsonException::fromJsonError(json_last_error());
        }

        $writeResult = @fwrite($this->handle, $jsonString);
        if ($writeResult === false) {
            throw new ResourceWriteException("JSON could not be written to resource.");
        }
    }

    /**
     * @param resource $handle
     *
     * @return bool
     */
    private function isStreamWritable($handle)
    {
        $meta = stream_get_meta_data($handle);
        $currentMode = $meta['mode'];

        $writeModes = ['r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+'];
        foreach ($writeModes as $writeMode) {
            if (strpos($currentMode, $writeMode) !== false) {
                return true;
            }
        }

        return false;
    }
}
