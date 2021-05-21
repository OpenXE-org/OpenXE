<?php

namespace Xentral\Modules\Api\Exception;

use Throwable;
use Xentral\Modules\Api\Http\Exception\HttpException;
use Xentral\Modules\Api\Error\ApiError;

class BadRequestException extends HttpException
{
    public function __construct(
        $message = 'Bad request',
        $code = ApiError::CODE_BAD_REQUEST,
        Throwable $previous = null,
        array $errors = array()
    ) {
        parent::__construct(400, $message, $code, $previous, $errors);
    }
}
