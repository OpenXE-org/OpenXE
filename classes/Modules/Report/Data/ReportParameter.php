<?php

namespace Xentral\Modules\Report\Data;

use JsonSerializable;
use Xentral\Modules\Report\Exception\OptionParseException;
use Xentral\Modules\Report\Exception\ParameterNameException;

class ReportParameter implements JsonSerializable
{
    /** @var string $varname */
    private $varname;

    /** @var string $displayname */
    private $displayname;

    /** @var string $defaultValue */
    private $defaultValue;

    /** @var ReportParameterOptionValue[] $options */
    private $options;

    /** @var string $description */
    private $description;

    /** @var bool $editable */
    private $editable;

    /** @var int $id */
    private $id;

    /** @var mixed|null */
    private $temporaryValue;

    /** @var string $controlType */
    private $controlType;

    /**
     * @param string                       $varname
     * @param string                       $defaultValue
     * @param string                       $displayname
     * @param ReportParameterOptionValue[] $options
     * @param string                       $description
     * @param bool                         $editable
     * @param int                          $id
     * @param string                       $controlType
     */
    public function __construct(
        $varname,
        $defaultValue,
        $displayname = '',
        $options = [],
        $description = '',
        $editable = true,
        $id = 0,
        $controlType = ''
    ) {
        $this->setVarName($varname);
        $this->displayname = $displayname;
        $this->defaultValue = $defaultValue;
        $this->options = $options;
        $this->description = $description;
        $this->editable = $editable;
        $this->id = $id;
        $this->controlType = $controlType;
    }

    /**
     * @param array $data
     *
     * @return ReportParameter|null
     */
    public static function fromDbState($data)
    {
        if (!isset($data['varname'])) {
            return null;
        }
        if (!isset($data['default_value'])) {
            return null;
        }
        $varname = (string)$data['varname'];
        $default = (string)$data['default_value'];
        $displayname = '';
        if (isset($data['displayname'])) {
            $displayname = $data['displayname'];
        }
        $options = [];
        if (isset($data['options'])) {
            if (is_string($data['options'])) {
                $options = self::parseOptions($data['options']);
            }
            if (is_array($data['options'])) {
                foreach ($data['options'] as $option) {
                    $key = array_keys($option)[0];
                    $options[] = new ReportParameterOptionValue($option[$key], $key);
                }
            }
        }
        $description = '';
        if (isset($data['description'])) {
            $description = $data['description'];
        }
        $editable = true;
        if (isset($data['editable']) && ($data['editable'] === 0 || $data['editable'] === false)) {
            $editable = false;
        }
        $id = 0;
        if (isset($data['id'])) {
            $id = $data['id'];
        }
        $controlType = '';
        if (isset($data['control_type'])) {
            $controlType = $data['control_type'];
        }

        return new self($varname, $default, $displayname, $options, $description, $editable, $id, $controlType);
    }

    /**
     * @param string $optionsLine
     *
     * @return ReportParameterOptionValue[]
     */
    public static function parseOptions($optionsLine)
    {
        if (empty($optionsLine)) {
            return [];
        }

        $optionObjects = [];

        $options = explode(',', $optionsLine);
        foreach ($options as $option) {

            $option = trim($option);
            if (empty($option)) {
                throw new OptionParseException('Invalid Options Format. Unable to parse.');
            }

            $value = explode(':', $option);
            if (count($value) === 2) {
                if (empty(trim($value[0])) || empty(trim($value[1]))) {
                    throw new OptionParseException('Invalid Options Format. Unable to parse.');
                }
                $optionObjects[] = new ReportParameterOptionValue(trim($value[1]), trim($value[0]));
            } else {
                $optionObjects[] = new ReportParameterOptionValue(trim($value[0]));
            }
        }

        return $optionObjects;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'varname'       => $this->getVarname(),
            'default_value' => $this->getDefaultValue(),
            'displayname'   => $this->getDisplayname(),
            'options'       => $this->getOptionsAsString(),
            'description'   => $this->getDescription(),
            'editable'      => $this->isEditable(),
            'id'            => $this->getId(),
            'control_type'  => $this->getControlType(),
        ];
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     */
    public function jsonSerialize()
    {
        return [
            'varname'       => $this->varname,
            'default_value' => $this->defaultValue,
            'displayname'   => $this->displayname,
            'options'       => $this->options,
            'description'   => $this->description,
            'editable'      => $this->editable,
            'control_type'  => $this->controlType,
        ];
    }

    /**
     * @param mixed|null $value
     */
    public function setTemporaryValue($value)
    {
        $this->temporaryValue = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        if ($this->temporaryValue !== null) {
            return $this->temporaryValue;
        }

        return $this->getDefaultValue();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getVarname()
    {
        return $this->varname;
    }

    /**
     * @return string
     */
    public function getDisplayname()
    {
        return $this->displayname;
    }

    /**
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return ReportParameterOptionValue[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param ReportParameterOptionValue[] $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getOptionsAsString()
    {
        $optionstrings = [];
        foreach ($this->options as $option) {
            $optionstrings[] = (string)$option;
        }

        return implode(',', $optionstrings);
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
    public function getControlType()
    {
        return $this->controlType;
    }

    /**
     * @return bool
     */
    public function isEditable()
    {
        return $this->editable;
    }

    /**
     * @param string $varname
     */
    private function setVarName($varname)
    {
        if (!preg_match('/^[a-zA-Z]\w*$/', $varname)) {
            throw new ParameterNameException(sprintf('Invalid parameter name "%s"', 'name'));
        }

        $this->varname = $varname;
    }
}
