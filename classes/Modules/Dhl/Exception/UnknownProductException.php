<?php

namespace Xentral\Modules\Dhl\Exception;

use Throwable;

class UnknownProductException extends DhlBaseException
{
    public function __construct($message = "Falsch konfiguriertes Produkt", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
