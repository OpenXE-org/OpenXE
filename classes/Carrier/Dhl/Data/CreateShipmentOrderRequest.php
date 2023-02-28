<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Carrier\Dhl\Data;

class CreateShipmentOrderRequest
{
  public Version $Version;
  public ShipmentOrder $ShipmentOrder;
  public ?string $labelResponseType;
  public ?string $groupProfileName;
  public ?string $labelFormat;
  public ?string $labelFormatRetoure;
  public ?string $combinedPrinting;
}