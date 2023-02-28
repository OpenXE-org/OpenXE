<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

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