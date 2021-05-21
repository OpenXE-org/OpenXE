<?php

namespace Xentral\Modules\Api\Validator\Rule;

use Rakit\Validation\Rule;

class DecimalRule extends Rule
{
    /** @var string $message */
    protected $message = "The attribute ':attribute' must be a decimal or integer value.";

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function check($value)
    {
        return is_numeric((string)$value);
    }
}
