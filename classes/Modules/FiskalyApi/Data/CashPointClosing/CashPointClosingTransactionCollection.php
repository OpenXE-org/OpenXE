<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use IteratorAggregate;
use Countable;
use ArrayIterator;

class CashPointClosingTransactionCollection implements IteratorAggregate, Countable
{
    /** @var CashPointClosingTransaction[] $transactions */
    private $transactions = [];

    /**
     * CashPointClosingTransactionCollection constructor.
     *
     * @param array $transactions
     */
    public function __construct(array $transactions = [])
    {
        foreach ($transactions as $transaction) {
            $this->addTransaction($transaction);
        }
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
            $instance->addTransaction(CashPointClosingTransaction::fromApiResult($item));
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
            $instance->addTransaction(CashPointClosingTransaction::fromDbState($item));
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [];
        /** @var CashPointClosingTransaction $item */
        foreach ($this as $item) {
            $dbState[] = $item->toArray();
        }

        return $dbState;
    }

    /**
     * @param CashPointClosingTransaction $transaction
     */
    public function addTransaction(CashPointClosingTransaction $transaction): self
    {
        $this->transactions[] = CashPointClosingTransaction::fromDbState($transaction->toArray());

        return $this;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->transactions);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->transactions);
    }
}
