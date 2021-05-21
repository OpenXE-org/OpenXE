<?php

namespace Xentral\Modules\Api\Exception;

use Throwable;
use Xentral\Modules\Api\Error\ApiError;
use Xentral\Modules\Api\Http\Exception\HttpException;

class WebserverMisconfigurationException extends HttpException
{
    /**
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(
        $message = 'Webserver configuration incorrect',
        $code = ApiError::CODE_WEBSERVER_MISCONFIGURED,
        Throwable $previous = null
    ) {
        parent::__construct(500, $message, $code, $previous);
    }
}
