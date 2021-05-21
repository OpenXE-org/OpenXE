<?php

namespace Xentral\Components\Exporter\Csv;

use Iterator;
use Xentral\Components\Exporter\Exception\InvalidResourceException;
use Xentral\Components\Exporter\Exception\PhpExtensionMissingException;

final class CsvWriter
{
    /** @var resource $handle */
    private $handle;

    /** @var CsvConfig $config */
    private $config;

    /**
     * @param resource       $handle
     * @param CsvConfig|null $config
     *
     * @throws InvalidResourceException If resource is not writable or invalid
     * @throws PhpExtensionMissingException If mbstring is missing
     */
    public function __construct($handle, CsvConfig $config = null)
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
            $config = new CsvConfig();
        }

        $this->config = $config;
        $this->handle = $handle;
    }

    /**
     * @param array|Iterator $lines
     *
     * @return void
     */
    public function writeLines($lines)
    {
        foreach ($lines as $line) {
            $this->writeLine($line);
        }
    }

    /**
     * @param array $line
     *
     * @return void
     */
    public function writeLine($line)
    {
        if ($this->config->isForceEnclosureEnabled()) {
            $fields = $this->encloseAllValues($line);
            fwrite(
                $this->handle,
                sprintf(
                    "%s\n",
                    implode($this->config->getDelimiter(), $this->convertCharset($fields))
                )
            );

            return;
        }

        fputcsv(
            $this->handle,
            $this->convertCharset($line),
            $this->config->getDelimiter(),
            $this->config->getEnclosure(),
            $this->config->getEscapeChar()
        );
    }

    /***
     * @param array $line
     *
     * @return array
     */
    private function encloseAllValues($line)
    {
        $escapeChar = $this->config->getEscapeChar();
        $enclosure = $this->config->getEnclosure();
        $result = [];
        foreach ($line as $key => $value) {
            $value = str_replace($escapeChar . $enclosure, $enclosure, $value);
            $value = str_replace($enclosure, $escapeChar . $enclosure, $value);
            $result[$key] = sprintf('%1$s%2$s%1$s', $enclosure, $value);
        }

        return $result;
    }


    /**
     * @param array $line
     *
     * @return array
     */
    private function convertCharset($line)
    {
        if ($this->config->getSourceCharset() === $this->config->getTargetCharset()) {
            return $line; // No conversion needed
        }

        $result = [];

        foreach ($line as $key => $cellData) {
            $result[$key] = mb_convert_encoding(
                $cellData,
                $this->config->getTargetCharset(),
                $this->config->getSourceCharset()
            );
        }

        return $result;
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
