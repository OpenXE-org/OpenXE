<?php

namespace Xentral\Carrier\Dhl\Data;

class ExportDocPosition
{
  public string $description;
  public string $countryCodeOrigin;
  public string $customsTariffNumber;
  public int $amount;
  public float $netWeightInKG;
  public float $customsValue;
}