<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

class CashPointClosingPayment
{
    /** @var float $fullAmount */
    private $fullAmount;

    /** @var float $cashAmount */
    private $cashAmount;

    /** @var CashAmountByCurrencyCollection $cashAmountsByCurrency */
    private $cashAmountsByCurrency;

    /** @var CashPointClosingPaymentTypeCollection $paymentTypes */
    private $paymentTypes;

    /**
     * CashPointClosingPayment constructor.
     *
     * @param float                                 $fullAmount
     * @param float                                 $cashAmount
     * @param CashAmountByCurrencyCollection        $cashAmountsByCurrency
     * @param CashPointClosingPaymentTypeCollection $paymentTypes
     */
    public function __construct(
        float $fullAmount,
        float $cashAmount,
        CashAmountByCurrencyCollection $cashAmountsByCurrency,
        CashPointClosingPaymentTypeCollection $paymentTypes
    ) {
        $this->setFullAmount($fullAmount);
        $this->setCashAmount($cashAmount);
        $this->setCashAmountsByCurrency($cashAmountsByCurrency);
        $this->setPaymentTypes($paymentTypes);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            (float)$apiResult->full_amount,
            (float)$apiResult->cash_amount,
            CashAmountByCurrencyCollection::fromApiResult($apiResult->cash_amounts_by_currency),
            CashPointClosingPaymentTypeCollection::fromApiResult($apiResult->payment_types)
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
            (float)$dbState['full_amount'],
            (float)$dbState['cash_amount'],
            CashAmountByCurrencyCollection::fromDbState($dbState['cash_amounts_by_currency']),
            CashPointClosingPaymentTypeCollection::fromDbState($dbState['payment_types'])
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'full_amount'              => $this->getFullAmount(),
            'cash_amount'              => $this->getCashAmount(),
            'cash_amounts_by_currency' => $this->getCashAmountsByCurrency()->toArray(),
            'payment_types'            => $this->getPaymentTypes()->toArray(),
        ];
    }

    /**
     * @return float
     */
    public function getFullAmount(): float
    {
        return $this->fullAmount;
    }

    /**
     * @param float $fullAmount
     */
    public function setFullAmount(float $fullAmount): void
    {
        $this->fullAmount = (float)number_format($fullAmount, 2, '.', '');
    }

    /**
     * @return mixed
     */
    public function getCashAmount()
    {
        return $this->cashAmount;
    }

    /**
     * @param float $cashAmount
     */
    public function setCashAmount(float $cashAmount): void
    {
        $this->cashAmount = (float)number_format($cashAmount, 2, '.', '');
    }

    /**
     * @return CashAmountByCurrencyCollection
     */
    public function getCashAmountsByCurrency(): CashAmountByCurrencyCollection
    {
        return CashAmountByCurrencyCollection::fromDbState($this->cashAmountsByCurrency->toArray());
    }

    /**
     * @param CashAmountByCurrencyCollection $cashAmountsByCurrency
     */
    public function setCashAmountsByCurrency(CashAmountByCurrencyCollection $cashAmountsByCurrency): void
    {
        $this->cashAmountsByCurrency = CashAmountByCurrencyCollection::fromDbState($cashAmountsByCurrency->toArray());
    }

    /**
     * @return CashPointClosingPaymentTypeCollection
     */
    public function getPaymentTypes(): CashPointClosingPaymentTypeCollection
    {
        return CashPointClosingPaymentTypeCollection::fromDbState($this->paymentTypes->toArray());
    }

    /**
     * @param CashPointClosingPaymentTypeCollection $paymentTypes
     */
    public function setPaymentTypes(CashPointClosingPaymentTypeCollection $paymentTypes): void
    {
        $this->paymentTypes = CashPointClosingPaymentTypeCollection::fromDbState($paymentTypes->toArray());
    }
}
