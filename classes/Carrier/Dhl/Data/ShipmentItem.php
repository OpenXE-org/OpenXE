<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Carrier\Dhl\Data;

class ShipmentItem
{
  public float $weightInKG;
  public ?int $lengthInCM;
  public ?int $widthInCM;
  public ?int $heightInCM;
}