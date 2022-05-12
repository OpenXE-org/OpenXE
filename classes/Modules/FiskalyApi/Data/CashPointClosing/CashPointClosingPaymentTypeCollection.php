<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class CashPointClosingPaymentTypeCollection implements IteratorAggregate, Countable
{
    /** @var CashPointClosingPaymentType[] $paymentTypes */
    private $paymentTypes = [];

    /**
     * CashPointClosingPaymentTypeCollection constructor.
     *
     * @param CashPointClosingPaymentType[] $paymentTypes
     */
    public function __construct(array $paymentTypes = [])
    {
        foreach ($paymentTypes as $paymentType) {
            $this->addPaymentType($paymentType);
        }
    }

    /**
     * @param CashPointClosingPaymentType $paymentType
     */
    public function addPaymentType(CashPointClosingPaymentType $paymentType): void
    {
        $this->paymentTypes[] = CashPointClosingPaymentType::fromDbState($paymentType->toArray());
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
            $instance->addPaymentType(CashPointClosingPaymentType::fromApiResult($item));
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
            $instance->addPaymentType(CashPointClosingPaymentType::fromDbState($item));
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [];
        /** @var CashPointClosingPaymentType $item */
        foreach ($this as $item) {
            $dbState[] = $item->toArray();
        }

        return $dbState;
    }

    /**
     * @param CashPointClosingPaymentTypeCollection $paymentTypeCollection
     *
     * @return $this
     */
    public function combine(self $paymentTypeCollection): self
    {
        /** @var CashPointClosingPaymentType $paymentType */
        foreach($paymentTypeCollection as $paymentType) {
            $keys = $this->findKeysForType($paymentType);
            if(empty($keys)) {
                $this->addPaymentType($paymentType);
                continue;
            }
            $key = reset($keys);
            $this->paymentTypes[$key]->setAmount($paymentType->getAmount() + $this->paymentTypes[$key]->getAmount());
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function getGrouped(): self
    {
        $instance = new self();
        $byType = [];
        /** @var CashPointClosingPaymentType $paymentType */
        foreach($this as $paymentType) {
            $type = $paymentType->getType();
            if(!isset($byType[$type])) {
                $byType[$type] = CashPointClosingPaymentType::fromDbState($paymentType->toArray());
            } else {
                $byType[$type]->setAmount($byType[$type]->getAmount() + $paymentType->getAmount());
            }
        }
        foreach($byType as $paymentType) {
            $instance->addPaymentType($paymentType);
        }

        return $instance;
    }

    /**
     * @param string      $type
     * @param float       $amount
     * @param string      $currencyCode
     * @param string|null $name
     * @param float|null  $foreignAmount
     *
     * @return array
     */
    private function findKeysForType(CashPointClosingPaymentType $paymentTypeToFind): array
    {
        $keys = [];
        /**
         * @var int $key
         * @var CashPointClosingPaymentType $paymentType
         */
        foreach($this as $key => $paymentType) {
            if($paymentType->getType() !== $paymentTypeToFind->getType()) {
                continue;
            }
            if($paymentType->getCurrencyCode() !==  $paymentTypeToFind->getCurrencyCode()) {
                continue;
            }
            if($paymentType->getName() !== $paymentTypeToFind->getName()) {
                continue;
            }
            if($paymentType->getForeignAmount() !== $paymentTypeToFind->getForeignAmount()) {
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
