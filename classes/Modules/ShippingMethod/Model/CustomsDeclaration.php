<?php

namespace Xentral\Modules\ShippingMethod\Model;

class CustomsDeclaration {
    public ShipmentType $shipmentType;

    public string $invoiceNumber;
    /**
     * @var CustomsDeclarationItem[]
     */
    public array $positions = [];
}

class CustomsDeclarationItem {
    public string $description;
    public int $quantity;
    public string $hsCode;
    public string $originCountryCode;
    public float $itemValue;
    public float $itemWeight;
}
