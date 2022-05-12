<?php

namespace Xentral\Modules\Hubspot\Validators;

use Xentral\Modules\Hubspot\Exception\HubspotException;

final class DealValidator implements ValidatorInterface
{

    /** @var string */
    private $rules;
    private $data;

    public function __construct($rules = 'default')
    {
        $this->rules = $rules;
    }


    public function isValid($data = [])
    {
        $validatorMethod = 'validatorRule' . ucfirst($this->rules);
        if (!method_exists($this, $validatorMethod)) {
            throw new HubspotException(sprintf('Validator method %s is missing', $validatorMethod));
        }
        $this->data = $data;
        $rules = $this->{$validatorMethod}();
        foreach ($rules as $field => $rule) {
            if (array_key_exists('rule', $rule)) {
                $validation = call_user_func($rule['rule'], $data);
                if (!$validation && (!empty($this->data[$field]) || !empty($rule['required']))) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function validatorRuleDefault()
    {
        return [
            'dealname'  => [
                'rule'     => static function ($data) {
                    return !empty($data['dealname']) && is_string($data['dealname']);
                },
                'required' => true,
                'message'  => sprintf('%s should be a non empty String', 'name'),
            ],
            'dealstage' => [
                'rule'     => static function ($data) {
                    return !empty($data['dealstage']) && is_string($data['dealstage']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'Deal stage'),
            ],
            'pipeline'  => [
                'rule'     => static function ($data) {
                    return !empty($data['pipeline']) && is_string($data['pipeline']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'Pipeline'),
            ],

            'hubspot_owner_id' => [
                'rule'     => static function ($data) {
                    return !empty($data['hubspot_owner_id']) && is_numeric($data['hubspot_owner_id']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'hubspot_owner_id'),
            ],

            'closedate' => [
                'rule'     => static function ($data) {
                    return !empty($data['closedate']) && is_numeric($data['closedate']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'closedate'),
            ],

            'dealtype' => [
                'rule'     => static function ($data) {
                    return !empty($data['dealtype']) && is_string($data['dealtype']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'dealtype'),
            ],

            'amount' => [
                'rule'     => static function ($data) {
                    return !empty($data['amount']) && is_numeric($data['amount']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'amount'),
            ],
        ];
    }

    public function getData()
    {
        return array_filter($this->data, static function ($value) {
            return $value !== null && trim($value) !== '';
        });
    }
}
