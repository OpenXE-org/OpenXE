<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Transaction\VatAmount;

class SpecialRate1VatAmount extends BaseVatAmount
{
    public function __construct(float $amount)
    {
        parent::__construct('SPECIAL_RATE_1', $amount);
    }
}
