<?php

namespace Xentral\Modules\Api\Validator\Rule;

use Rakit\Validation\Rule;

/**
 * Regel prüft ob alle Zeichen Grossbuchstaben sind.
 *
 * - Deutsche Umlaute sind nicht zulässig.
 * - Regel wurde für Fälle wie Länder ISO-Code erstellt.
 */
class UpperRule extends Rule
{
    /** @var string $message */
    protected $message = "The attribute ':attribute' must be in uppercase letter.";

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function check($value)
    {
        return ctype_upper($value);
    }
}
