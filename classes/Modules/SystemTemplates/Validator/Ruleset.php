<?php

namespace Xentral\Modules\SystemTemplates\Validator;

use Closure;
use Xentral\Modules\SystemTemplates\Validator\Exception\SystemTemplateValidatorException;


final class Ruleset
{
    /** @var SystemTemplateValidatorInterface $validator */
    private $validator;

    /** @var string $configMethod */
    private $configMethod;

    /** @var string */
    const DEFAULT_RULES_CONFIGURATOR = 'validateDefault';

    /**
     * @param string|null $configMethod
     */
    public function __construct($configMethod = null)
    {
        if (null === $configMethod) {
            $configMethod = static::DEFAULT_RULES_CONFIGURATOR;
        }
        $this->configMethod = $configMethod;
    }

    /**
     * @param SystemTemplateValidatorInterface $validator
     * @param string                           $configMethod
     */
    public function setRules(SystemTemplateValidatorInterface $validator, $configMethod = null)
    {
        $this->validator = $validator;

        $configMethod = $configMethod === null ? $this->configMethod : $configMethod;
        if (!method_exists($this->validator, $configMethod)) {
            throw new SystemTemplateValidatorException(
                sprintf('Validate Config method %s is missing', $configMethod)
            );
        }
        $ruleConfigs = $this->validator->{$configMethod}();
        if (!empty($ruleConfigs)) {
            foreach ($ruleConfigs as $fieldName => $config) {
                if (!is_string($fieldName) || empty($fieldName)) {
                    throw new SystemTemplateValidatorException(
                        sprintf('Field name is missing in Configuration at index %s', $fieldName)
                    );
                }

                if (!array_key_exists('rule', $config) || empty($config['rule'])) {
                    throw new SystemTemplateValidatorException(
                        sprintf('Rule is missing in Configuration at index "%s', $fieldName)
                    );
                }

                $this->addRule($fieldName, $config);
            }
        }
    }

    /**
     * @param string $fieldName
     * @param array  $config
     *
     * @return void
     */
    private function addRule($fieldName, $config)
    {
        $callbackResponse = false;

        $rule = $config['rule'];
        $message = !empty($config['message']) ? $config['message'] : sprintf('%s is Invalid', $fieldName);

        if (is_array($rule) && !method_exists($this->validator, $rule[0])) {
            throw new SystemTemplateValidatorException(sprintf('Custom check method %s is missing !', $rule[0]));
        }

        if (array_key_exists('required', $config) && in_array($config['required'], [true, 1], true)) {
            $this->validator->setMandatoryFieldName($fieldName);
        }

        if (is_array($rule)) {
            $arg = empty($rule[1]) ? [] : [$rule[1]];
            $callbackResponse = call_user_func_array([$this->validator, $rule[0]], $arg);

        } elseif ($rule instanceof Closure) {
            $callbackResponse = $rule($this->validator->getData());
        }

        if ($callbackResponse !== true) {
            $this->validator->addError($message);
        }
    }
}
