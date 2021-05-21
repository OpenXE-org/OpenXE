<?php

namespace Xentral\Modules\Hubspot\Validators;

use Xentral\Modules\Hubspot\Exception\HubspotException;

final class ContactValidator implements ValidatorInterface
{

    /** @var string */
    private $rules;
    private $data;

    public function __construct($rules = 'default')
    {
        $this->rules = $rules;
    }

    /**
     * @return array
     */
    public function validatorRuleDefault()
    {
        return [
            'email'     => [
                'rule'     => static function ($data) {
                    return !empty($data['email']) && is_string($data['email']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'Email'),
            ],
            'firstname' => [
                'rule'     => static function ($data) {
                    return !empty($data['firstname']) && is_string($data['firstname']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'First name'),
            ],
            'lastname'  => [
                'rule'     => static function ($data) {
                    return !empty($data['lastname']) && is_string($data['lastname']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'Last name'),
            ],

            'website' => [
                'rule'     => static function ($data) {
                    return !empty($data['website']) && is_string($data['website']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'Website'),
            ],

            'company' => [
                'rule'     => static function ($data) {
                    return !empty($data['company']) && is_string($data['company']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'company'),
            ],

            'phone' => [
                'rule'     => static function ($data) {
                    return !empty($data['phone']) && is_string($data['phone']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'phone'),
            ],

            'address' => [
                'rule'     => static function ($data) {
                    return !empty($data['address']) && is_string($data['address']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'Address'),
            ],

            'city'  => [
                'rule'     => static function ($data) {
                    return !empty($data['city']) && is_string($data['city']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'city'),
            ],
            'state' => [
                'rule'     => static function ($data) {
                    return !empty($data['state']) && is_string($data['state']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'state'),
            ],
            'zip'   => [
                'rule'     => static function ($data) {
                    return !empty($data['zip']) && is_string($data['zip']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'zip'),
            ],
        ];
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function isValid($data = [])
    {
        $this->data = $data;
        $validatorMethod = 'validatorRule' . ucfirst($this->rules);
        if (!method_exists($this, $validatorMethod)) {
            throw new HubspotException(sprintf('Validator method %s is missing', $validatorMethod));
        }

        $rules = $this->{$validatorMethod}();
        foreach ($rules as $field => $rule) {
            if (array_key_exists('rule', $rule)) {
                $validation = call_user_func($rule['rule'], $this->data);
                if (!$validation && (!empty($this->data[$field]) || !empty($rule['required']))) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getData()
    {
        return array_filter($this->data, static function ($value) {
            return $value !== null && trim($value) !== '';
        });
    }

}

