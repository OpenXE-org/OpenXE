<?php

namespace Xentral\Modules\Api\Exception;

use Throwable;
use Xentral\Modules\Api\Http\Exception\HttpException;
use Xentral\Modules\Api\Error\ApiError;

class MethodNotAllowedException extends HttpException
{
    public function __construct(
        array $allowedMethods,
        $message = 'Method not allowed',
        $code = ApiError::CODE_METHOD_NOT_ALLOWED,
        Throwable $previous = null
    ) {
        $message = sprintf('Method is not allowed. Allowed: %s', implode(', ', $allowedMethods));

        parent::__construct(405, $message, $code, $previous);
    }
}
