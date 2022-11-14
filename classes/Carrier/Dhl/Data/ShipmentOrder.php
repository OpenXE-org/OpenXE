<?php

namespace Xentral\Carrier\Dhl\Data;

class ShipmentOrder
{
  public string $sequenceNumber;
  public Shipment $Shipment;
  public ?Serviceconfiguration $PrintOnlyIfCodeable;
}