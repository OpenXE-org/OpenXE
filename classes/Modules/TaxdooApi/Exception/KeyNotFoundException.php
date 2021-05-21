<?php

namespace Xentral\Modules\TaxdooApi\Exception;

use Throwable;

class KeyNotFoundException extends TaxdooFatalExcepion
{
    /**
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        if (empty($message)) {
            $message = 'Kein Zugangsschlüssel konfiguriert';
        }

        parent::__construct($message, $code, $previous);
    }
}
