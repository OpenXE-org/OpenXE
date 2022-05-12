<?php

namespace Xentral\Modules\Api\Exception;

use Throwable;
use Xentral\Modules\Api\Http\Exception\HttpException;
use Xentral\Modules\Api\Error\ApiError;

class ValidationErrorException extends HttpException
{
    public function __construct(
        array $errors,
        $message = 'Validation error',
        $code = ApiError::CODE_VALIDATION_ERROR,
        Throwable $previous = null
    ) {
        parent::__construct(400, $message, $code, $previous, $errors);
    }
}
