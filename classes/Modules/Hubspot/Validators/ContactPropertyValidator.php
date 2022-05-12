<?php

namespace Xentral\Modules\Hubspot\Validators;

final class ContactPropertyValidator implements ValidatorInterface
{

    /** @var string */
    private $rules;

    public function __construct($rules = 'default')
    {
        $this->rules = $rules;
    }

    public function isValid($data = [])
    {
        // TODO: Implement isValid() method.
    }

    public function validatorRuleDefault()
    {
        // TODO: Implement validatorRuleDefault() method.
    }
}
