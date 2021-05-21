<?php

namespace Xentral\Modules\Api\Validator\Rule;

use Rakit\Validation\Rule;

/**
 * Regel prüft ob alle Zeichen Kleinbuchstaben sind.
 *
 * - Deutsche Umlaute sind nicht zulässig.
 * - Regel wurde für Fälle wie Sprach ISO-Code erstellt.
 */
class LowerRule extends Rule
{
    /** @var string $message */
    protected $message = "The attribute ':attribute' must be in lowercase letter.";

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function check($value)
    {
        return ctype_lower($value);
    }
}
