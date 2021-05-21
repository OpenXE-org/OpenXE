<?php

namespace Xentral\Components\Exporter\Csv;

final class CsvConfig
{
    /** @var string $delimiter */
    private $delimiter;

    /** @var string $enclosure */
    private $enclosure;

    /** @var string $escapeChar */
    private $escapeChar;

    /** @var string $sourceCharset */
    private $sourceCharset;

    /** @var string $targetCharset */
    private $targetCharset;

    /** @var bool $forceEnclosureEnabled */
    private $forceEnclosureEnabled;

    /**
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escapeChar
     * @param string $targetCharset
     * @param string $sourceCharset
     * @param bool   $forceEnclosureEnabled
     */
    public function __construct(
        $delimiter = ',',
        $enclosure = '"',
        $escapeChar = "\\",
        $targetCharset = 'UTF-8',
        $sourceCharset = 'UTF-8',
        $forceEnclosureEnabled = false
    ) {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escapeChar = $escapeChar;
        $this->targetCharset = $targetCharset;
        $this->sourceCharset = $sourceCharset;
        $this->forceEnclosureEnabled = $forceEnclosureEnabled;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * @return string
     */
    public function getEnclosure()
    {
        return $this->enclosure;
    }

    /**
     * @return string
     */
    public function getEscapeChar()
    {
        return $this->escapeChar;
    }

    /**
     * @return string
     */
    public function getSourceCharset()
    {
        return $this->sourceCharset;
    }

    /**
     * @return string
     */
    public function getTargetCharset()
    {
        return $this->targetCharset;
    }

    /**
     * @param string $delimiter
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
    }

    /**
     * @param string $enclosure
     */
    public function setEnclosure($enclosure)
    {
        $this->enclosure = $enclosure;
    }

    /**
     * @param string $escapeChar
     */
    public function setEscapeChar($escapeChar)
    {
        $this->escapeChar = $escapeChar;
    }

    /**
     * @param string $sourceCharset
     */
    public function setSourceCharset($sourceCharset)
    {
        $this->sourceCharset = $sourceCharset;
    }

    /**
     * @param string $targetCharset
     */
    public function setTargetCharset($targetCharset)
    {
        $this->targetCharset = $targetCharset;
    }

    /**
     * @return bool
     */
    public function isForceEnclosureEnabled()
    {
        return $this->forceEnclosureEnabled;
    }

    /**
     * @param bool $forceEnclosureEnabled
     */
    public function setForceEnclosureEnabled($forceEnclosureEnabled)
    {
        $this->forceEnclosureEnabled = $forceEnclosureEnabled;
    }
}
