<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class SubLineItemCollection implements IteratorAggregate, Countable
{
    /** @var CashPointClosingTransactionSubLineItem[] $subLineItems */
    private $subLineItems = [];

    /**
     * SubLineItemCollection constructor.
     *
     * @param array $subLineItems
     */
    public function __construct(array $subLineItems = [])
    {
        foreach($subLineItems as $subLineItem) {
            $this->addSubLineItem($subLineItem);
        }
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        $instance = new self();
        foreach ($apiResult as $item) {
            $instance->addSubLineItem(CashPointClosingTransactionSubLineItem::fromApiResult($item));
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
            $instance->addSubLineItem(CashPointClosingTransactionSubLineItem::fromDbState($item));
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [];
        /** @var CashPointClosingTransactionSubLineItem $amountPerVat */
        foreach($this as $item) {
            $dbState[] = $item->toArray();
        }

        return $dbState;
    }

    /**
     * @param CashPointClosingTransactionSubLineItem $subLineItem
     */
    public function addSubLineItem(CashPointClosingTransactionSubLineItem $subLineItem): void
    {
        $this->subLineItems[] = CashPointClosingTransactionSubLineItem::fromDbState($subLineItem->toArray());
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->subLineItems);
    }

    public function count(): int
    {
        return count($this->subLineItems);
    }
}
