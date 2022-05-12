<?php

namespace Xentral\Modules\Api\Exception;

use Throwable;
use Xentral\Modules\Api\Http\Exception\HttpException;
use Xentral\Modules\Api\Error\ApiError;

class InvalidArgumentException extends HttpException
{
    public function __construct(
        $message = 'Invalid argument',
        $code = ApiError::CODE_INVALID_ARGUMENT,
        Throwable $previous = null
    ) {
        parent::__construct(400, $message, $code, $previous);
    }
}
