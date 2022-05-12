<?php

namespace Xentral\Components\Exporter\Json;

final class JsonConfig
{
    /** @var int $options */
    private $options;

    /**
     * @see https://www.php.net/manual/en/json.constants.php
     *
     * @param int $options
     */
    public function __construct($options = 0)
    {
        $this->options = $options;
    }

    /**
     * @return int
     */
    public function getOptions()
    {
        return $this->options;
    }
}
