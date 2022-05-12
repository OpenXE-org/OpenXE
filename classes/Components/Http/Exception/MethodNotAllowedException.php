<?php

namespace Xentral\Components\Http\Exception;

use Throwable;

class MethodNotAllowedException extends HttpException
{
    public function __construct(
        array $allowedMethods,
        $message = null,
        $code = 0,
        Throwable $previous = null
    ) {
        if ($message === null) {
            $message = sprintf('Method is not allowed. Allowed: %s', implode(', ', $allowedMethods));
        }

        parent::__construct(405, $message, $code, $previous);
    }
}
