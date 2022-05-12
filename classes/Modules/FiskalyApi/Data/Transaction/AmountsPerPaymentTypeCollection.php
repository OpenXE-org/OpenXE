<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\Transaction;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\AmountPerVatId;

class AmountsPerPaymentTypeCollection implements IteratorAggregate, Countable
{
    /** @var AmountsPerPaymentType[] $paymentTypes */
    private $paymentTypes = [];

    /**
     * CashPointClosingPaymentTypeCollection constructor.
     *
     * @param AmountsPerPaymentType[] $paymentTypes
     */
    public function __construct(array $paymentTypes = [])
    {
        foreach ($paymentTypes as $paymentType) {
            $this->addPaymentType($paymentType);
        }
    }

    /**
     * @param AmountsPerPaymentType $paymentType
     */
    public function addPaymentType(AmountsPerPaymentType $paymentType): void
    {
        $this->paymentTypes[] = AmountsPerPaymentType::fromDbState($paymentType->toArray());
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult($apiResult): self
    {
        $instance = new self();
        foreach ($apiResult as $item) {
            $instance->addPaymentType(AmountsPerPaymentType::fromApiResult($item));
        }

        return $instance;
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState): self
    {
        $instance = new self();
        foreach ($dbState as $item) {
            $instance->addPaymentType(AmountsPerPaymentType::fromDbState($item));
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [];
        /** @var AmountsPerPaymentType $item */
        foreach ($this as $item) {
            $dbState[] = $item->toArray();
        }

        return $dbState;
    }

    /**
     * @param AmountsPerPaymentType $paymentTypeCollection
     *
     * @return $this
     */
    public function combine(self $paymentTypeCollection): self
    {
        /** @var AmountsPerPaymentType $paymentType */
        foreach ($paymentTypeCollection as $paymentType) {
            $keys = $this->findKeysForType($paymentType);
            if (empty($keys)) {
                $this->addPaymentType($paymentType);
                continue;
            }
            $key = reset($keys);
            $amount = number_format(
                (float)$paymentType->getAmount() + (float)$this->paymentTypes[$key]->getAmount(),
                2,
                '.',
                ''
            );
            $this->paymentTypes[$key]->setAmount($amount);
        }

        return $this;
    }

    /**
     * @param string $paymentType
     *
     * @return $this
     */
    public function filterByType(string $paymentType): self
    {
        $instance = new self();
        /** @var AmountsPerPaymentType $item */
        foreach($this as $item) {
            if($item->getType() === $paymentType) {
                $instance->addPaymentType($item);
            }
        }

        return $instance;
    }

    /**
     * @param string $currencyCode
     *
     * @return float
     */
    public function getSum(string $currencyCode = 'EUR'): float
    {
        $sum = 0;
        /** @var AmountsPerPaymentType $item */
        foreach($this as $item) {
            if($item->getCurrencyCode() !== $currencyCode) {
                continue;
            }
            $sum += (float)$item->getAmount();
        }

        return $sum;
    }

    /**
     * @return array
     */
    public function getCurrencyCodes(): array
    {
        $currencyCodes = [];
        /** @var AmountsPerPaymentType $item */
        foreach($this as $item) {
            $currencyCode = $item->getCurrencyCode();
            if(!in_array($currencyCode, $currencyCodes, true)) {
                $currencyCodes[] = $currencyCode;
            }
        }

        return $currencyCodes;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->paymentTypes);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->paymentTypes);
    }

    /**
     * @param AmountsPerPaymentType $paymentTypeToFind
     *
     * @return array
     */
    private function findKeysForType(AmountsPerPaymentType $paymentTypeToFind): array
    {
        $keys = [];
        /**
         * @var int $key
         * @var AmountsPerPaymentType $paymentType
         */
        foreach ($this as $key => $paymentType) {
            if ($paymentType->getType() !== $paymentTypeToFind->getType()) {
                continue;
            }
            if ($paymentType->getCurrencyCode() !== $paymentTypeToFind->getCurrencyCode()) {
                continue;
            }

            $keys[] = $key;
        }

        return $keys;
    }
}
