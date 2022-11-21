<?php

namespace Xentral\Carrier\Dhl\Data;

class ReceiverNativeAddress
{
  public ?string $name2;
  public ?string $name3;
  public string $streetName;
  public ?string $streetNumber;
  public array|string|null $addressAddition;
  public ?string $dispatchingInformation;
  public string $zip;
  public string $city;
  public ?string $province;
  public ?Country $Origin;
}