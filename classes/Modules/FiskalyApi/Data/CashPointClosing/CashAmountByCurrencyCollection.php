<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class CashAmountByCurrencyCollection implements IteratorAggregate, Countable
{
    /** @var CashAmountByCurrency[] $cashAmountsByCurrecy */
    private $cashAmountsByCurrecy = [];

    /**
     * CashAmountByCurrencyCollection constructor.
     *
     * @param array $cashAmountsByCurrecy
     */
    public function __construct(array $cashAmountsByCurrecy = [])
    {
        foreach ($cashAmountsByCurrecy as $cashAmountByCurrecy) {
            $this->addAmountPerCurrecy($cashAmountByCurrecy);
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
            $instance->addAmountPerCurrecy(CashAmountByCurrency::fromApiResult($item));
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
            $instance->addAmountPerCurrecy(CashAmountByCurrency::fromDbState($item));
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [];
        /** @var CashAmountByCurrency $amountPerCurrency */
        foreach ($this as $amountPerCurrency) {
            $dbState[] = $amountPerCurrency->toArray();
        }

        return $dbState;
    }

    /**
     * @param CashAmountByCurrency $amountPerCurrency
     */
    public function addAmountPerCurrecy(CashAmountByCurrency $amountPerCurrency): void
    {
        $this->cashAmountsByCurrecy[] = CashAmountByCurrency::fromDbState($amountPerCurrency->toArray());
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->cashAmountsByCurrecy);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->cashAmountsByCurrecy);
    }
}
