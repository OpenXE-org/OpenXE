<?php

namespace Xentral\Carrier\Dhl\Data;

class NativeAddressNew
{
  public string $streetName;
  public ?string $streetNumber;
  public string $zip;
  public string $city;
  public ?Country $Origin;
}