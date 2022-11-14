<?php

namespace Xentral\Carrier\Dhl\Data;

class DeleteShipmentOrderResponse
{
  public Version $Version;
  public Statusinformation $Status;
  public ?DeletionState $DeletionState;
}