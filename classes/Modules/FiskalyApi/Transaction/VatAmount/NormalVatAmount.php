<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Transaction\VatAmount;

final class NormalVatAmount extends BaseVatAmount
{
    public function __construct(float $amount)
    {
        parent::__construct('NORMAL', $amount);
    }
}
