<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Validator;

use Xentral\Modules\Pipedrive\Exception\PipedriveValidatorException;

final class PipedrivePersonValidator implements PipedriveValidatorInterface
{
    /** @var string $rules */
    private $rules;

    /** @var array $data */
    private $data;

    /**
     * PipedrivePersonValidator constructor.
     *
     * @param string $rules
     */
    public function __construct(string $rules = 'default')
    {
        $this->rules = $rules;
    }

    /**
     * @inheritDoc
     */
    public function isValid(array $data = []): bool
    {
        $this->data = $data;
        $validatorMethod = 'validatorRule' . ucfirst($this->rules);
        if (!method_exists($this, $validatorMethod)) {
            throw new PipedriveValidatorException(sprintf('Validator method %s is missing', $validatorMethod));
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

    /**
     * @inheritDoc
     */
    public function validatorRuleDefault(): array
    {
        return [
            'email'      => [
                'rule'     => static function ($data) {
                    return !empty($data['email']) && is_string($data['email']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'Email'),
            ],
            'name' => [
                'rule'     => static function ($data) {
                    return !empty($data['name']) && is_string($data['name']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'Name'),
            ],
            'first_name' => [
                'rule'     => static function ($data) {
                    return !empty($data['first_name']) && is_string($data['first_name']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'First name'),
            ],
            'last_name'  => [
                'rule'     => static function ($data) {
                    return !empty($data['last_name']) && is_string($data['last_name']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'Last name'),
            ],

            'phone' => [
                'rule'     => static function ($data) {
                    return !empty($data['phone']) && is_string($data['phone']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'phone'),
            ],

            'label' => [
                'rule'     => static function ($data) {
                    return !empty($data['label']) && is_numeric($data['label']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty numeric', 'Pipedrive-Label:'),
            ],
        ];
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return array_filter(
            $this->data,
            static function ($value) {
                return is_numeric($value) || (is_string($value) && trim($value) !== '');
            }
        );
    }
}
