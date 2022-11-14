<?php

namespace Xentral\Carrier\Dhl\Data;

class PackStation
{
  public string $postNumber;
  public string $packstationNumber;
  public string $zip;
  public string $city;
  public ?string $province;
  public ?Country $Origin;
}