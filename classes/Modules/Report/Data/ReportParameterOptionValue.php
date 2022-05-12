<?php

namespace Xentral\Modules\Report\Data;

use JsonSerializable;

class ReportParameterOptionValue implements JsonSerializable
{
    /** @var string $value */
    private $value;

    /** @var string $description */
    private $description;

    /**
     * @param string $value
     * @param string $description
     */
    public function __construct($value, $description = null)
    {
        $this->value = $value;
        if (empty($description)) {
            $this->description = $this->value;
        } else {
            $this->description = $description;
        }
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s:%s', $this->getDescription(), $this->getValue());
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     */
    public function jsonSerialize()
    {
        return [$this->getDescription() => $this->getValue()];
    }
}
