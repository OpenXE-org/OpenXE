<?php

namespace Xentral\Modules\Resubmission\Exception;

use RuntimeException;

final class ValidationFailedException extends RuntimeException implements ResubmissionExceptionInterface
{
    /** @var array $errors */
    private $errors = [];

    /**
     * @param array $errors
     *
     * @return self
     */
    public static function fromErrors(array $errors)
    {
        $errorString = '';
        foreach ($errors as $propertyName => $propertyErrors) {
            $errorString .= implode("\r\n", $propertyErrors);
        }

        $exception = new self('Validation failed with following errors: ' . "\n\n" . $errorString);
        $exception->errors = $errors;

        return $exception;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
