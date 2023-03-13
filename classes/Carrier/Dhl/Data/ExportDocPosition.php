<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

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