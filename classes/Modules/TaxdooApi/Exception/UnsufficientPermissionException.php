<?php

namespace Xentral\Modules\TaxdooApi\Exception;

class UnsufficientPermissionException extends TaxdooFatalExcepion
{
    /**
     * @param string $key
     *
     * @return UnsufficientPermissionException
     */
    public static function fromKey($key)
    {
        return new self("Nicht ausreichende Berechtigungen: {$key}");
    }
}