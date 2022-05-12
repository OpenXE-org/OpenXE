<?php

namespace Xentral\Modules\Hubspot\Validators;

interface ValidatorInterface
{
    public function isValid($data=[]);
    public function validatorRuleDefault();
}
