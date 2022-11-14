<?php

namespace Xentral\Carrier\Dhl\Data;

class ShipmentItem
{
  public float $weightInKG;
  public ?int $lengthInCM;
  public ?int $widthInCM;
  public ?int $heightInCM;
}