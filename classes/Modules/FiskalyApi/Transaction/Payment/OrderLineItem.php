<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Transaction\Payment;

final class OrderLineItem
{
    /** @var float $quantity */
    private $quantity;

    /** @var string $text */
    private $text;

    /** @var float $pricePerUnit */
    private $pricePerUnit;

    public function __construct(float $quantity, string $text, float $pricePerUnit)
    {
        $this->quantity = $quantity;
        $this->text = $text;
        $this->pricePerUnit = $pricePerUnit;
    }

    /**
     * @return string
     */
    public function getQuantity(): string
    {
        return number_format($this->quantity, 2, '.', '');
    }

    /**
     * @param float $quantity
     */
    public function setQuantity(float $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return mb_substr($this->text, 0, 255);
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getPricePerUnit(): string
    {
        return number_format($this->pricePerUnit, 2, '.', '');
    }

    /**
     * @param float $pricePerUnit
     */
    public function setPricePerUnit(float $pricePerUnit): void
    {
        $this->pricePerUnit = $pricePerUnit;
    }


}
