<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Validator;

use Xentral\Modules\Pipedrive\Exception\PipedriveValidatorException;

final class PipedriveDealValidator implements PipedriveValidatorInterface
{
    /** @var string $rules */
    private $rules;

    /** @var array $data */
    private $data;

    /**
     * PipedriveDealValidator constructor.
     *
     * @param string $rule
     */
    public function __construct(string $rule = 'default')
    {
        $this->rules = $rule;
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
            'title'    => [
                'rule'     => static function ($data) {
                    return !empty($data['title']) && is_string($data['title']);
                },
                'required' => true,
                'message'  => sprintf('%s should be a non empty String', 'Title'),
            ],
            'value'    => [
                'rule'     => static function ($data) {
                    return !empty($data['value']) && is_string($data['value']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'Deal Value'),
            ],
            'currency' => [
                'rule'     => static function ($data) {
                    return !empty($data['currency']) && is_string($data['currency']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'currency'),
            ],

            'user_id' => [
                'rule'     => static function ($data) {
                    return !empty($data['user_id']) && is_numeric($data['user_id']);
                },
                'required' => false,
                'message'  => sprintf('%d should be a non empty String', 'user_id'),
            ],

            'person_id' => [
                'rule'     => static function ($data) {
                    return !empty($data['person_id']) && is_numeric($data['person_id']);
                },
                'required' => false,
                'message'  => sprintf('%d should be a non empty String', 'person_id'),
            ],

            'stage_id' => [
                'rule'     => static function ($data) {
                    return !empty($data['stage_id']) && is_numeric($data['stage_id']);
                },
                'required' => false,
                'message'  => sprintf('%d should be a non empty String', 'stage_id'),
            ],

            'probability' => [
                'rule'     => static function ($data) {
                    return !empty($data['probability']) && is_numeric($data['probability']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'probability'),
            ],

            'status' => [
                'rule'     => static function ($data) {
                    return !empty($data['status']) && in_array($data['status'], ['open', 'won', 'lost', 'deleted']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'status'),
            ],

            'lost_reason' => [
                'rule'     => static function ($data) {
                    return !empty($data['lost_reason']) && is_string($data['lost_reason']);
                },
                'required' => false,
                'message'  => sprintf('%s should be a non empty String', 'lost reason'),
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
