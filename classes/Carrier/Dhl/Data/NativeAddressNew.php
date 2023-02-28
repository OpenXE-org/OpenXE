<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Carrier\Dhl\Data;

class NativeAddressNew
{
  public string $streetName;
  public ?string $streetNumber;
  public string $zip;
  public string $city;
  public ?Country $Origin;
}