<?php

namespace Xentral\Modules\AmazonVendorDF\Data;

class ShippingLabel implements \JsonSerializable
{
    /** @var string */
    private $purchaseOrderNumber;
    /** @var string */
    private $encodedLabelData;
    /** @var string */
    private $labelFormat;
    /** @var string */
    private $trackingNumber;

    public function __construct(string $purchaseOrderNumber, string $encodedLabelData, string $labelFormat = 'PNG')
    {
        //@TODO check if we need to implement multiple labels per order
        $this->purchaseOrderNumber = $purchaseOrderNumber;
        $this->encodedLabelData = $encodedLabelData;
        $this->labelFormat = $labelFormat;
    }

    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }

    public function setTrackingNumber(string $trackingNumber): self
    {
        $this->trackingNumber = $trackingNumber;

        return $this;
    }

    public function hasTrackingNumber(): bool
    {
        return $this->trackingNumber !== null;
    }

    public function getEncodedLabelData(): string
    {
        return $this->encodedLabelData;
    }

    public function jsonSerialize(): array
    {
        return [
            'purchase_order_number' => $this->purchaseOrderNumber,
            'tracking_number' => $this->trackingNumber,
            'encodedLabelData' => $this->encodedLabelData
        ];
    }
}
