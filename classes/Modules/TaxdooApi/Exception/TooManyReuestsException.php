<?php

namespace Xentral\Modules\TaxdooApi\Exception;

class TooManyReuestsException extends TaxdooFatalExcepion
{
    /** @var int $remaining */
    private $remaining;

    /**
     * @param int $timeout
     *
     * @return TooManyReuestsException
     */
    public static function fromTimeout($timeout)
    {
        $e = new self("Too many requests ($timeout secs timeout)");
        $e->remaining = $timeout;

        return $e;
    }

    /**
     * @return int
     */
    public function getRemaining()
    {
        return $this->remaining;
    }
}
