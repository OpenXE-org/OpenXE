<?php

namespace Xentral\Modules\AmazonVendorDF\Data;

class Price
{
    /** @var string */
    private $currency;
    /** @var float */
    private $amount;

    public function __construct(string $currency, float $amount)
    {
        $this->currency = $currency;
        $this->amount = $amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function toArray(): array
    {
        return [
            'currencyCode' => $this->currency,
            'amount'       => $this->amount,
        ];
    }
}
