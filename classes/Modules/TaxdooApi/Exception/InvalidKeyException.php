<?php

namespace Xentral\Modules\TaxdooApi\Exception;

class InvalidKeyException extends TaxdooFatalExcepion
{
    /**
     * @param string $key
     *
     * @return InvalidKeyException
     */
    public static function fromKey($key)
    {
        return new self("Ungültiger key: {$key}");
    }
}
