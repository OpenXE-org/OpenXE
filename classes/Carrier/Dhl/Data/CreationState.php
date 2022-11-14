<?php

namespace Xentral\Carrier\Dhl\Data;

class CreationState
{
    public string $sequenceNumber;
    public ?string $shipmentNumber;
    public ?string $returnShipmentNumber;
    public LabelData $LabelData;
}