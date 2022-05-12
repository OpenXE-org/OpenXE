<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\Transaction;

use Xentral\Modules\FiskalyApi\Exception\InvalidArgumentException;

class AmountsPerVatType
{
    /** @var string $vatRate */
    private $vatRate;

    /** @var string $amount */
    private $amount;

    /**
     * AmountsPerVatType constructor.
     *
     * @param string $vatRate
     * @param string $amount
     */
    public function __construct(
        string $vatRate,
        string $amount
    ) {
        $this->setVatRate($vatRate);
        $this->setAmount($amount);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            $apiResult->vat_rate,
            $apiResult->amount
        );
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState): self
    {
        return new self(
            $dbState['vat_rate'],
            $dbState['amount']
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [
            'vat_rate' => $this->getVatRate(),
            'amount'   => $this->getAmount(),
        ];

        return $dbState;
    }

    /**
     * @return string
     */
    public function getVatRate(): string
    {
        return $this->vatRate;
    }

    /**
     * @param string $vatRate
     */
    public function setVatRate(string $vatRate): void
    {
        if(!in_array($vatRate, ['NORMAL','REDUCED_1','SPECIAL_RATE_1','SPECIAL_RATE_2', 'NULL'])) {
            throw new InvalidArgumentException("invalid vatRate: '{$vatRate}");
        }
        $this->vatRate = $vatRate;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(string $amount): void
    {
        if(!preg_match('/^-?\d+(\.\d{2,64})$/', $amount)) {
            throw new InvalidArgumentException("invalid amount-format: '{$amount}");
        }
        $this->amount = $amount;
    }
}
