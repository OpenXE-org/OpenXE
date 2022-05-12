<?php

namespace Xentral\Modules\Api\Validator;

use Rakit\Validation\Validator as RakitValidator;

class Validator extends RakitValidator
{
    /**
     * @see ApiContainer::createValidatorService
     *
     * @param array $messages
     */
    public function __construct(array $messages = [])
    {
        $messages = [
            'db_value' => "The attribute ':attribute' has to be present in database. " .
                "The value ':value' is not present in table ':table'.",
            'between' => "The attribute :attribute must be between :min and :max.",
            'boolean' => "The attribute ':attribute' must be a boolean value.",
            'date' => "The attribute ':attribute' is not valid date format. Format ':format' is required.",
            'time' => "The attribute ':attribute' is not valid time format. Format ':format' is required.",
            'decimal' => "The attribute ':attribute' must be a decimal or integer value.",
            'integer' => "The attribute ':attribute' must be an integer value.",
            'length' => "The attribute ':attribute' must have the length :length.",
            'lower' => "The attribute ':attribute' must be in lowercase letter.",
            'in' => "The attribute ':attribute' is not allowing the value ':value'.",
            'max' => "The attribute ':attribute' has a maximum value ':max'.",
            'min' => "The attribute ':attribute' has a minimum value ':min'.",
            'numeric' => "The attribute ':attribute' must be numeric.",
            'not_present' => "The attribute ':attribute' is not allowed.",
            'present' => "The attribute ':attribute' must be present",
            'required' => "The attribute ':attribute' is required.",
            'unique' => "The attribute ':attribute' has to be unique. The value ':value' is already in use.",
            'upper' => "The attribute ':attribute' must be in uppercase letter.",
        ];

        parent::__construct($messages);

        // Attribute nicht ucfirst-en
        $this->useHumanizedKeys = false;
    }
}
