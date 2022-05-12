<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Transaction\Payment;

final class NonCashPayment extends BasePayment
{
    public function __construct(float $amount)
    {
        parent::__construct('NON_CASH', $amount);
    }
}
