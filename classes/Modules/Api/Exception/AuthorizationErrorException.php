<?php

namespace Xentral\Modules\Api\Exception;

use Throwable;
use Xentral\Modules\Api\Http\Exception\HttpException;

class AuthorizationErrorException extends HttpException
{
    public function __construct($message = 'Authorization error', $code = 0, Throwable $previous = null)
    {
        parent::__construct(401, $message, $code, $previous);
    }
}
