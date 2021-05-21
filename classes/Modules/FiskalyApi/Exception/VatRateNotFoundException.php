<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Exception;

class VatRateNotFoundException extends FiskalyApiBaseException
{
    public static function fromPercentage(float $percentage){
        return new VatRateNotFoundException("VAT rate {$percentage} not found");
    }
}
