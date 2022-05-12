<?php


namespace Xentral\Modules\SystemTemplates\Validator;

interface SystemTemplateValidatorInterface
{
    /** @param string|null $methodName */
    public function isValid($methodName = null);

    public function getErrors();

    public function setData($data);

    public function validateDefault();

    public function setMandatoryFieldName($fieldName);

    public function addError($error);

    public function getData();

    public function applyRules($ruleMethod = null);
}