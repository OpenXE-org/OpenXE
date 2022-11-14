<?php

namespace Xentral\Carrier\Dhl\Data;

class Country
{
  public ?string $country;
  public string $countryISOCode;
  public ?string $state;

  public static function Create(string $isoCode, ?string $state = null):Country {
    $obj = new Country();
    $obj->countryISOCode = $isoCode;
    $obj->state = $state;
    return $obj;
  }

}