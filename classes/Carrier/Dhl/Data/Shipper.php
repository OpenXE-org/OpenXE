<?php

namespace Xentral\Carrier\Dhl\Data;

class Shipper
{
  public Name $Name;
  public NativeAddressNew $Address;
  public ?Communication $Communication;

  public function __construct()
  {
    $this->Name = new Name();
    $this->Address = new NativeAddressNew();
  }
}