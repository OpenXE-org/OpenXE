<?php

namespace Xentral\Modules\Ebay\Data;

class StockLogingVariationData
{

    /** @var string */
    protected $sku;
    /** @var int */
    protected $quantity;

    public function __construct(string $sku, int $quantity)
    {
        $this->sku = $sku;
        $this->quantity = $quantity;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): StockLogingVariationData
    {
        $this->sku = $sku;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): StockLogingVariationData
    {
        $this->quantity = $quantity;

        return $this;
    }

}
