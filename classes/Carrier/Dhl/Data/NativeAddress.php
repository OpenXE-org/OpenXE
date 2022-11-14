<?php

namespace Xentral\Carrier\Dhl\Data;

class NativeAddress
{
  public string $streetName;
  public ?string $streetNumber;

  /**
   * @var string[]
   */
  public array $addressAddition;
  public ?string $dispatchingInformation;
  public string $zip;
  public string $city;
  public ?string $province;
  public ?Country $Origin;
}