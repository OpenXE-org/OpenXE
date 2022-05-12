<?php

namespace Xentral\Modules\Api\Validator\Rule;

use Rakit\Validation\Rule;

class BooleanRule extends Rule
{
    /** @var string $message */
    protected $message = "The attribute ':attribute' must be a boolean value.";

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function check($value)
    {
        if ($value === true || $value === 1 || $value === '1') {
            return true; // true = Ist Boolean
        }
        if ($value === false || $value === 0 || $value === '0') {
            return true; // true = Ist Boolean
        }

        // 'true' und 'false' als String ist nicht zulässig, da die Datenbank diese Werte nicht casten kann.

        return false; // Kein Boolean
    }
}
