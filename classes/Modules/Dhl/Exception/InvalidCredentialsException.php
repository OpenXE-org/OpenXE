<?php

namespace Xentral\Modules\Dhl\Exception;

use Throwable;

class InvalidCredentialsException extends DhlBaseException
{
    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Fehlerhafte Zugangsdaten ({$message})", $code, $previous);
    }
}
