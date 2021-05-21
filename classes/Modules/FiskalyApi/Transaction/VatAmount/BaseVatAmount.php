<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Transaction\VatAmount;

use Xentral\Modules\FiskalyApi\Exception\VatRateNotFoundException;

abstract class BaseVatAmount
{
    /** @var string */
    private $vatType;
    /** @var float */
    private $amount;

    /**
     * BaseVatAmount constructor.
     *
     * @param string $vatType
     * @param float  $amount
     */
    protected function __construct(string $vatType, float $amount)
    {
        $this->vatType = $vatType;
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getVatType(): string
    {
        return $this->vatType;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @param float $amountToAdd
     *
     * @return void
     */
    public function add(float $amountToAdd): void
    {
        $this->amount += $amountToAdd;
    }

    /**
     * @param float $percentage
     * @param float $amount
     *
     * @throws VatRateNotFoundException
     *
     * @return BaseVatAmount
     */
    public static function fromPercentage(float $percentage, float $amount): BaseVatAmount
    {
        $mapping = [
            19.0 => NormalVatAmount::class,
            7.0  => Reduced1VatAmount::class,
            10.7 => SpecialRate1VatAmount::class,
            5.5  => SpecialRate2VatAmount::class,
            0    => NullVatAmount::class
        ];

        $class = $mapping[$percentage];

        if(empty($class)){
            throw VatRateNotFoundException::fromPercentage($percentage);
        }

        return new $class($amount);
    }
}
