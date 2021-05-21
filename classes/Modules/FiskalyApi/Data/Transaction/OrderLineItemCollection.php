<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\Transaction;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class OrderLineItemCollection implements IteratorAggregate, Countable
{
    /** @var OrderLineItem[] $lineItems */
    private $lineItems = [];

    /**
     * OrderLineItemCollection constructor.
     *
     * @param array $lineItems
     */
    public function __construct(array $lineItems = [])
    {
        foreach($lineItems as $lineItem) {
            $this->addLineItem($lineItem);
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
        foreach($apiResult as $item) {
            $instance->addLineItem(OrderLineItem::fromApiResult($item));
        }

        return $instance;
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState = []): self
    {
        $instance = new self();
        foreach($dbState as $item) {
            $instance->addLineItem(OrderLineItem::fromDbState($item));
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [];
        foreach($this as $item) {
            $dbState[] = $item->toArray();
        }

        return $dbState;
    }

    public function addLineItem(OrderLineItem $lineItem): void
    {
        $this->lineItems[] = OrderLineItem::fromDbState($lineItem->toArray());;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->lineItems);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->lineItems);
    }
}
