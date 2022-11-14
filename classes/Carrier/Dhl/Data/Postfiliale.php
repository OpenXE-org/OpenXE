<?php

namespace Xentral\Carrier\Dhl\Data;

class Postfiliale
{
  public string $postfilialeNumber;
  public string $postNumber;
  public string $zip;
  public string $city;
  public ?Country $Origin;
}