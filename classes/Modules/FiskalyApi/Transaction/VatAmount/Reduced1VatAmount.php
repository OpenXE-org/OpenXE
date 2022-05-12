<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Transaction\VatAmount;

class Reduced1VatAmount extends BaseVatAmount
{
    public function __construct(float $amount)
    {
        parent::__construct('REDUCED_1', $amount);
    }
}
