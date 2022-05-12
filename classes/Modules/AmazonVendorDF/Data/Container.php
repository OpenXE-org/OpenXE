<?php

namespace Xentral\Modules\AmazonVendorDF\Data;

class Container
{
    /** @var string */
    private $containerType;
    /** @var string */
    private $containerIdentifier;
    /** @var string */
    private $length;
    /** @var string */
    private $width;
    /** @var string */
    private $height;
    /** @var string */
    private $unitOfMeasure;
    /** @var array */
    private $items = [];

    public function __construct(string $containerIdentifier, string $containerType = 'carton')
    {
        $this->containerIdentifier = $containerIdentifier;
        $this->containerType = $containerType;
    }

    public function setDimensions(string $length, string $width, string $height, string $unitOfMeasure = 'CM')
    {
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
        $this->unitOfMeasure = $unitOfMeasure;
    }

    public function addItem(
        string $itemSequenceNumber,
        string $buyerProductIdentifier,
        string $vendorProductIdentifier,
        Quantity $packedQuantity
    ) {
        $quantity = $packedQuantity->toArray();
        unset($quantity['unitSize']);
        $this->items[] = [
            'itemSequenceNumber'      => (int)$itemSequenceNumber,
            'buyerProductIdentifier'  => $buyerProductIdentifier,
            'vendorProductIdentifier' => $vendorProductIdentifier,
            'packedQuantity'          => $quantity,
        ];
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function toArray()
    {
        return [
            'containerType'       => $this->containerType,
            'containerIdentifier' => $this->containerIdentifier,
            'dimensions'          => [
                'length'        => $this->length,
                'width'         => $this->width,
                'height'        => $this->height,
                'unitOfMeasure' => $this->unitOfMeasure,
            ],
            'weight'              => [
                'unitOfMeasure' => 'KG',
                'value'         => '1',
            ],
            'packedItems'         => $this->items,
        ];
    }
}
