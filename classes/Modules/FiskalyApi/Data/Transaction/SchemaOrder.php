<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\Transaction;

class SchemaOrder
{
    /** @var OrderLineItemCollection $lineItems */
    private $lineItems;

    /**
     * SchemaOther constructor.
     */
    public function __construct(OrderLineItemCollection $lineItemCollection)
    {
        $this->setLineItems($lineItemCollection);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(OrderLineItemCollection::fromApiResult($apiResult->line_items));
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState): self
    {
        return new self(OrderLineItemCollection::fromDbState($dbState['line_items']));
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return ['line_items' => $this->lineItems->toArray()];
    }

    /**
     * @return OrderLineItemCollection
     */
    public function getLineItems(): OrderLineItemCollection
    {
        return OrderLineItemCollection::fromDbState($this->lineItems->toArray());
    }

    /**
     * @param OrderLineItemCollection $lineItems
     */
    public function setLineItems(OrderLineItemCollection $lineItems): void
    {
        $this->lineItems = OrderLineItemCollection::fromDbState($lineItems->toArray());
    }
}
