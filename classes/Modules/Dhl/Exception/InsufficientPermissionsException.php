<?php

namespace Xentral\Modules\Dhl\Exception;

use Throwable;

class InsufficientPermissionsException extends DhlBaseException
{
    public function __construct($message = "Unzureichende Rechte", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
