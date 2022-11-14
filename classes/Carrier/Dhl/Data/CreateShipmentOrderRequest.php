<?php

namespace Xentral\Carrier\Dhl\Data;

class CreateShipmentOrderRequest
{
  public Version $Version;
  public ShipmentOrder $ShipmentOrder;
  public ?string $labelResponseType;
  public ?string $groupProfileName;
  public ?string $labelFormat;
  public ?string $labelFormatRetoure;
  public ?string $combinedPrinting;
}