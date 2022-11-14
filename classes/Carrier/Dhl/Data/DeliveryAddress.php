<?php

namespace Xentral\Carrier\Dhl\Data;

class DeliveryAddress
{
  public ?NativeAddress $NativeAddress;
  public ?Postfiliale $PostOffice;
  public ?PackStation $PackStation;
  public ?string $streetNameCode;
  public ?string $streetNumberCode;
}