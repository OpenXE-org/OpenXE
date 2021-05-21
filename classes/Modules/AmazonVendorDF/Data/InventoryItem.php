<?php

namespace Xentral\Modules\AmazonVendorDF\Data;

class InventoryItem
{
    /** @var Quantity */
    private $quantity;
    /** @var string|null */
    private $vendorProductIdentifier;
    /** @var bool|null */
    private $isObsolete;
    /** @var string */
    private $buyerProductIdentifier;

    public function __construct(
        Quantity $quantity,
        ?string $vendorProductIdentifier = null,
        ?bool $isObsolete = false,
        ?string $buyerProductIdentifier = null
    ) {
        $this->quantity = $quantity;
        $this->vendorProductIdentifier = $vendorProductIdentifier;
        $this->isObsolete = $isObsolete;
        $this->buyerProductIdentifier = $buyerProductIdentifier;
    }

    public function toArray(): array
    {
        return array_filter(
            [
                'buyerProductIdentifier'  => $this->buyerProductIdentifier,
                'vendorProductIdentifier' => $this->vendorProductIdentifier,
                'availableQuantity'       => $this->quantity->toArray(),
                'isObsolete'              => $this->isObsolete,
            ]
        );
    }
}
