<?php

namespace Xentral\Modules\Api\Validator\Rule;

use Rakit\Validation\Rule;

class NotPresentRule extends Rule
{
    /** @var bool $implicit */
    protected $implicit = true;

    /** @var string $message */
    protected $message = 'The attribute \':attribute\' is not allowed.';

    /**
     * @param mixed $value
     */
    public function check($value)
    {
        return !$this->validation->hasValue($this->attribute->getKey());
    }
}
