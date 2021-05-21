<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Transaction\VatAmount;

class NullVatAmount extends BaseVatAmount
{
    public function __construct(float $amount)
    {
        parent::__construct('NULL', $amount);
    }
}
