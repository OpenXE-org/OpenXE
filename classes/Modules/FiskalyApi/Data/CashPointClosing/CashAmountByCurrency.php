<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use Xentral\Modules\FiskalyApi\Data\Transaction\AmountsPerPaymentType;
use Xentral\Modules\FiskalyApi\Exception\InvalidArgumentException;

class CashAmountByCurrency
{
    /** @var string $currencyCode */
    private $currencyCode;

    /** @var float $amount */
    private $amount;

    /**
     * CashAmountByCurrency constructor.
     *
     * @param float  $amount
     * @param string $currencyCode
     */
    public function __construct(float $amount, string $currencyCode = 'EUR')
    {
        $this->ensureCurrency($currencyCode);
        $this->setCurrencyCode($currencyCode);
        $this->setAmount($amount);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self((float)$apiResult->amount, $apiResult->currency_code);
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState): self
    {
        return new self(
            (float)$dbState['amount'], empty($dbState['currency_code']) ? 'EUR' : $dbState['currency_code']
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'currency_code' => $this->getCurrencyCode(),
            'amount'        => $this->getAmount(),
        ];
    }

    /**
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    /**
     * @param string $currencyCode
     */
    public function setCurrencyCode(string $currencyCode): void
    {
        $this->ensureCurrency($currencyCode);
        $this->currencyCode = $currencyCode;
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
        $this->amount = (float)number_format($amount, 2, '.', '');
    }


    /**
     * @param string $currencyCode
     */
    private function ensureCurrency(string $currencyCode): void
    {
        if (!in_array(
            $currencyCode,
            AmountsPerPaymentType::getAllowedCurrencies(),
            true
        )) {
            throw new InvalidArgumentException("invalid currency {$currencyCode}");
        }
    }
}
