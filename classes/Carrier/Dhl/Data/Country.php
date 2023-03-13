<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

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