<?php

namespace Xentral\Modules\Api\Validator\Rule;

use Rakit\Validation\MissingRequiredParameterException;
use Rakit\Validation\Rule;

/**
 * Regel prüft ob ein valides Zeitformat vorliegt.
 */
class TimeRule extends Rule
{
    /** @var string $message */
    protected $message = "The attribute ':attribute' is not valid time format. Format ':format' is required.";

    /** @var array $fillable_params */
    protected $fillable_params = ['format'];

    /** @var array $params */
    protected $params = [
        'format' => 'H:i:s',
    ];

    /**
     * @param mixed $value
     *
     * @throws MissingRequiredParameterException
     *
     * @return bool
     */
    public function check($value)
    {
        $this->requireParameters($this->fillable_params);

        $format = $this->parameter('format');

        return date_create_from_format('Y-m-d ' . $format, '2019-01-01 ' . $value) !== false;
    }
}
