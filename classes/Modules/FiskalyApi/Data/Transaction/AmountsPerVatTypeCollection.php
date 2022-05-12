<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\Transaction;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class AmountsPerVatTypeCollection implements IteratorAggregate, Countable
{
    /** @var AmountsPerVatType[] $paymentTypes */
    private $paymentTypes = [];

    /**
     * AmountsPerVatTypeCollection constructor.
     *
     * @param AmountsPerVatType[] $paymentTypes
     */
    public function __construct(array $paymentTypes = [])
    {
        foreach ($paymentTypes as $paymentType) {
            $this->addPaymentType($paymentType);
        }
    }

    /**
     * @param AmountsPerVatType $paymentType
     */
    public function addPaymentType(AmountsPerVatType $paymentType): void
    {
        $this->paymentTypes[] = AmountsPerVatType::fromDbState($paymentType->toArray());
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
            $instance->addPaymentType(AmountsPerVatType::fromApiResult($item));
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
            $instance->addPaymentType(AmountsPerVatType::fromDbState($item));
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [];
        /** @var AmountsPerVatType $item */
        foreach ($this as $item) {
            $dbState[] = $item->toArray();
        }

        return $dbState;
    }

    /**
     * @param AmountsPerVatType $amountsPerVatType
     *
     * @return $this
     */
    public function combine(self $amountsPerVatType): self
    {
        /** @var AmountsPerVatType $amountPerVatRate */
        foreach($amountsPerVatType as $amountPerVatRate) {
            $keys = $this->findKeysForType($amountPerVatRate);
            if(empty($keys)) {
                $this->addPaymentType($amountPerVatRate);
                continue;
            }
            $key = reset($keys);
            $amount = (float)$this->paymentTypes[$key]->getAmount() + (float)$amountPerVatRate->getAmount();
            $this->paymentTypes[$key]->setAmount(
                number_format($amount, 2, '.', '')
            );
        }

        return $this;
    }

    /**
     * @param AmountsPerVatType $paymentTypeToFind
     *
     * @return array
     */
    private function findKeysForType(AmountsPerVatType $paymentTypeToFind): array
    {
        $keys = [];
        /**
         * @var int $key
         * @var AmountsPerVatType $amountPerVatRate
         */
        foreach($this as $key => $amountPerVatRate) {
            if($amountPerVatRate->getVatRate() !== $paymentTypeToFind->getVatRate()) {
                continue;
            }

            $keys[] = $key;
        }

        return $keys;
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
}
