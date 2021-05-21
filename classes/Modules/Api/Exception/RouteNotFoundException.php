<?php

namespace Xentral\Modules\Api\Exception;

use Throwable;
use Xentral\Modules\Api\Http\Exception\HttpException;
use Xentral\Modules\Api\Error\ApiError;

class RouteNotFoundException extends HttpException
{
    public function __construct(
        $message = 'Route not found',
        $code = ApiError::CODE_ROUTE_NOT_FOUND,
        Throwable $previous = null
    ) {
        parent::__construct(404, $message, $code, $previous);
    }
}
