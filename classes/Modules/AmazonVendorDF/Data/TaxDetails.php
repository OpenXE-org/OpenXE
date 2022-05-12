<?php

namespace Xentral\Modules\AmazonVendorDF\Data;

class TaxDetails
{
    /** @var string */
    private $taxType;
    /** @var string */
    private $taxRate;
    /** @var Price */
    private $taxAmount;
    /** @var Price */
    private $taxableAmount;

    public function __construct(string $taxType, string $taxRate, Price $taxAmount, Price $taxableAmount)
    {
        $this->taxType = $taxType;
        $this->taxRate = $taxRate;
        $this->taxAmount = $taxAmount;
        $this->taxableAmount = $taxableAmount;
    }

    public function toArray()
    {
        return [
            'taxType'       => $this->taxType,
            'taxRate'       => $this->taxRate,
            'taxAmount'     => $this->taxAmount->toArray(),
            'taxableAmount' => $this->taxableAmount->toArray(),
        ];
    }
}
