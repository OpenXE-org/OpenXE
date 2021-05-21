<?php

namespace Xentral\Modules\Api\Exception;

use Throwable;
use Xentral\Modules\Api\Http\Exception\HttpException;
use Xentral\Modules\Api\Error\ApiError;

class ResourceNotFoundException extends HttpException
{
    public function __construct(
        $message = 'Resource not found',
        $code = ApiError::CODE_RESOURCE_NOT_FOUND,
        Throwable $previous = null
    ) {
        parent::__construct(404, $message, $code, $previous);
    }
}
