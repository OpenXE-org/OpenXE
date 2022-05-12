<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Transaction\Payment;

abstract class BasePayment
{
    /** @var string */
    private $paymentType;
    /** @var float */
    private $amount;

    /**
     * BasePayment constructor.
     *
     * @param string $paymentType
     * @param float  $amount
     */
    protected function __construct(string $paymentType, float $amount)
    {
        $this->paymentType = $paymentType;
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getPaymentType(): string {
        return $this->paymentType;
    }

    /**
     * @return float
     */
    public function getAmount(): float {
        return $this->amount;
    }
}
