<?php

namespace Xentral\Carrier\Dhl\Data;

class CreateShipmentOrderResponse
{
  public Version $Version;
  public Statusinformation $Status;
  public ?CreationState $CreationState;
}