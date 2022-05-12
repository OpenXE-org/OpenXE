<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use Xentral\Modules\FiskalyApi\Data\Transaction\AmountsPerPaymentType;
use Xentral\Modules\FiskalyApi\Exception\InvalidArgumentException;

class CashPointClosingPaymentType
{
    private $type;

    private $currencyCode;

    private $amount;

    private $name;

    private $foreignAmount;

    /**
     * CashPointClosingPaymentType constructor.
     *
     * @param string      $type
     * @param float       $amount
     * @param string      $currencyCode
     * @param string|null $name
     * @param float|null  $foreignAmount
     */
    public function __construct(
        string $type,
        float $amount,
        string $currencyCode = 'EUR',
        ?string $name = null,
        ?float $foreignAmount = null
    ) {
        $this->ensureType($type);
        $this->ensureCurrency($currencyCode);
        $this->setType($type);
        $this->setAmount($amount);
        $this->setCurrencyCode($currencyCode);
        $this->setName($name);
        $this->setForeignAmount($foreignAmount);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            $apiResult->type,
            (float)$apiResult->amount,
            $apiResult->currency_code,
            $apiResult->name ?? null,
            $apiResult->foreign_amount ?? null
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
            $dbState['type'],
            (float)$dbState['amount'],
            $dbState['currency_code'] ?? 'EUR',
            $dbState['name'] ?? null,
            $dbState['foreign_amount'] ?? null
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [
            'type'          => $this->getType(),
            'amount'        => $this->getAmount(),
            'currency_code' => $this->getCurrencyCode(),
        ];
        if ($this->name !== null) {
            $dbState['name'] = $this->getName();
        }
        if ($this->foreignAmount !== null) {
            $dbState['foreign_amount'] = $this->getForeignAmount();
        }

        return $dbState;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->ensureType($type);
        $this->type = $type;
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
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name === null ? null : mb_substr($name, 0, 60);
    }

    /**
     * @return float|null
     */
    public function getForeignAmount(): ?float
    {
        return $this->foreignAmount;
    }

    /**
     * @param float|null $foreignAmount
     */
    public function setForeignAmount(?float $foreignAmount): void
    {
        $this->foreignAmount = $foreignAmount === null ? null : (float)number_format($foreignAmount, 2, '.', '');
    }


    /**
     * @param string $type
     */
    private function ensureType(string $type): void
    {
        if (
        !in_array(
            $type,
            ['Bar', 'Unbar', 'ECKarte', 'Kreditkarte', 'ElZahlungsdienstleister', 'GuthabenKarte', 'Keine']
        )) {
            throw new InvalidArgumentException("invalid type {$type}");
        }
    }

    /**
     * @param string $currency
     */
    private function ensureCurrency(string $currency): void
    {
        if (!in_array(
            $currency,
            AmountsPerPaymentType::getAllowedCurrencies(),
            true
        )) {
            throw new InvalidArgumentException("invalid currency {$currency}");
        }
    }
}
