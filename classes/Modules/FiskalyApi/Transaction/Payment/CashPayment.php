<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Transaction\Payment;

final class CashPayment extends BasePayment
{
    public function __construct(float $amount)
    {
        parent::__construct('CASH', $amount);
    }
}
