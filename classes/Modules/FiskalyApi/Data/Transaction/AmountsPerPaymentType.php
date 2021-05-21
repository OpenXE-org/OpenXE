<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\Transaction;

use Xentral\Modules\FiskalyApi\Exception\InvalidArgumentException;

class AmountsPerPaymentType
{
    private $paymentType;

    private $currencyCode;

    private $amount;

    /**
     * CashPointClosingPaymentType constructor.
     *
     * @param string      $paymentType
     * @param string      $amount
     * @param string|null $currencyCode
     */
    public function __construct(
        string $paymentType,
        string $amount,
        ?string $currencyCode = null
    ) {
        $this->ensureType($paymentType);
        $this->ensureCurrency($currencyCode);
        $this->setType($paymentType);
        $this->setAmount($amount);
        $this->setCurrencyCode($currencyCode);
    }

    /**
     * @return string[]
     */
    public static function getAllowedCurrencies(): array
    {
        return [
            'AED',
            'AFN',
            'ALL',
            'AMD',
            'ANG',
            'AOA',
            'ARS',
            'AUD',
            'AWG',
            'AZN',
            'BAM',
            'BBD',
            'BDT',
            'BGN',
            'BHD',
            'BIF',
            'BMD',
            'BND',
            'BOB',
            'BOV',
            'BRL',
            'BSD',
            'BTN',
            'BWP',
            'BYN',
            'BYR',
            'BZD',
            'CAD',
            'CDF',
            'CHE',
            'CHF',
            'CHW',
            'CLF',
            'CLP',
            'CN',
            'COP',
            'COU',
            'CRC',
            'CUC',
            'CUP',
            'CVE',
            'CZK',
            'DJF',
            'DKK',
            'DOP',
            'DZD',
            'EGP',
            'ERN',
            'ETB',
            'EUR',
            'FJD',
            'FKP',
            'GBP',
            'GEL',
            'GHS',
            'GIP',
            'GMD',
            'GNF',
            'GTQ',
            'GYD',
            'HKD',
            'HNL',
            'HRK',
            'HTG',
            'HUF',
            'IDR',
            'ILS',
            'INR',
            'IQD',
            'IRR',
            'ISK',
            'JMD',
            'JOD',
            'JPY',
            'KES',
            'KGS',
            'KHR',
            'KMF',
            'KPW',
            'KRW',
            'KWD',
            'KYD',
            'KZT',
            'LAK',
            'LBP',
            'LKR',
            'LRD',
            'LSL',
            'LYD',
            'MAD',
            'MDL',
            'MGA',
            'MKD',
            'MMK',
            'MNT',
            'MOP',
            'MRO',
            'MUR',
            'MVR',
            'MWK',
            'MXN',
            'MXV',
            'MYR',
            'MZN',
            'NAD',
            'NGN',
            'NIO',
            'NOK',
            'NPR',
            'NZD',
            'OMR',
            'PAB',
            'PEN',
            'PGK',
            'PHP',
            'PKR',
            'PLN',
            'PYG',
            'QAR',
            'RON',
            'RSD',
            'RUB',
            'RWF',
            'SAR',
            'SBD',
            'SCR',
            'SDG',
            'SSP',
            'SEK',
            'SGD',
            'SHP',
            'SLL',
            'SOS',
            'SRD',
            'STD',
            'SVC',
            'SYP',
            'SZL',
            'THB',
            'TJS',
            'TMT',
            'TND',
            'TOP',
            'TRY',
            'TTD',
            'TWD',
            'TZS',
            'UAH',
            'UGX',
            'USD',
            'UYI',
            'UYU',
            'UZS',
            'VEF',
            'VND',
            'VUV',
            'WST',
            'XAF',
            'XCD',
            'XOF',
            'XPF',
            'XSU',
            'YER',
            'ZAR',
            'ZMW',
            'ZWL',
        ];
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            $apiResult->payment_type,
            $apiResult->amount,
            $apiResult->currency_code ?? null
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
            $dbState['payment_type'],
            $dbState['amount'],
            $dbState['currency_code'] ?? null
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [
            'payment_type'  => $this->getType(),
            'amount'        => $this->getAmount(),
            'currency_code' => $this->getCurrencyCode(),
        ];

        return $dbState;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->paymentType;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->ensureType($type);
        $this->paymentType = $type;
    }

    /**
     * @return string|null
     */
    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    /**
     * @param string|null $currencyCode
     */
    public function setCurrencyCode(?string $currencyCode): void
    {
        $this->ensureCurrency($currencyCode);
        $this->currencyCode = $currencyCode;
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

    /**
     * @param string $type
     */
    private function ensureType(string $type): void
    {
        if (
        !in_array(
            $type,
            ['CASH', 'NON_CASH']
        )) {
            throw new InvalidArgumentException("invalid paymentType {$type}");
        }
    }

    /**
     * @param string|null $currency
     */
    private function ensureCurrency(?string $currency): void
    {
        if($currency === null) {
            return;
        }
        if (!in_array(
            $currency,
            self::getAllowedCurrencies(),
            true
        )) {
            throw new InvalidArgumentException("invalid currency {$currency}");
        }
    }
}
