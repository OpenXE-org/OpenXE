<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

class CashPointClosingCashStatement
{
    /** @var BusinessCaseCollection $businessCases */
    private $businessCases;

    /** @var CashPointClosingPayment $payment */
    private $payment;

    /**
     * CashPointClosingCashStatement constructor.
     *
     * @param BusinessCaseCollection  $businessCases
     * @param CashPointClosingPayment $payment
     */
    public function __construct(BusinessCaseCollection $businessCases, CashPointClosingPayment $payment)
    {
        $this->setBusinessCases($businessCases);
        $this->setPayment($payment);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            BusinessCaseCollection::fromApiResult($apiResult->business_cases),
            CashPointClosingPayment::fromApiResult($apiResult->payment)
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
            BusinessCaseCollection::fromDbState($dbState['business_cases']),
            CashPointClosingPayment::fromDbState($dbState['payment'])
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'business_cases' => $this->getBusinessCases()->toArray(),
            'payment'        => $this->getPayment()->toArray(),
        ];
    }

    /**
     * @return BusinessCaseCollection
     */
    public function getBusinessCases(): BusinessCaseCollection
    {
        return BusinessCaseCollection::fromDbState($this->businessCases->toArray());
    }

    /**
     * @param BusinessCaseCollection $businessCases
     */
    public function setBusinessCases(BusinessCaseCollection $businessCases): void
    {
        $this->businessCases = BusinessCaseCollection::fromDbState($businessCases->toArray());
    }

    /**
     * @return CashPointClosingPayment
     */
    public function getPayment(): CashPointClosingPayment
    {
        return CashPointClosingPayment::fromDbState($this->payment->toArray());
    }

    /**
     * @param CashPointClosingPayment $payment
     */
    public function setPayment(CashPointClosingPayment $payment): void
    {
        $this->payment = CashPointClosingPayment::fromDbState($payment->toArray());
    }
}
