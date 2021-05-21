<?php

namespace Xentral\Modules\AmazonVendorDF\Data;

class Warehouse
{
    /** @var string */
    private $warehouseId;
    /** @var Address */
    private $address;

    public function __construct(string $warehouseId)
    {
        $this->warehouseId = $warehouseId;
    }

    public function setAddress(Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function hasNoAddress(): bool
    {
        return $this->address === null;
    }

    public function getWarehouseId(): string
    {
        return $this->warehouseId;
    }

    public function toArray()
    {
        return [
            'partyId' => $this->warehouseId,
            'address' => $this->address->toArray(),
        ];
    }
}
