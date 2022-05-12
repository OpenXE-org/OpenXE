<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use IteratorAggregate;
use Countable;
use ArrayIterator;

class CashPointClosingTransactionLineCollection implements IteratorAggregate, Countable
{
    /** @var CashPointClosingTransactionLine[] $lines */
    private $lines = [];


    /**
     * CashPointClosingTransactionLineCollection constructor.
     *
     * @param CashPointClosingTransactionLine[] $paymentTypes
     */
    public function __construct(array $lines = [])
    {
        foreach ($lines as $line) {
            $this->addLine($line);
        }
    }

    /**
     * @param CashPointClosingTransactionLine $line
     */
    public function addLine(CashPointClosingTransactionLine $line): void
    {
        $this->lines[] = CashPointClosingTransactionLine::fromDbState($line->toArray());
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
            $instance->addLine(CashPointClosingTransactionLine::fromApiResult($item));
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
            $instance->addLine(CashPointClosingTransactionLine::fromDbState($item));
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [];
        /** @var CashPointClosingTransactionLine $item */
        foreach ($this as $item) {
            $dbState[] = $item->toArray();
        }

        return $dbState;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->lines);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->lines);
    }
}
