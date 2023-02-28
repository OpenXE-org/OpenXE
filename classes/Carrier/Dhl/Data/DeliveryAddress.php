<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Carrier\Dhl\Data;

class DeliveryAddress
{
  public ?NativeAddress $NativeAddress;
  public ?Postfiliale $PostOffice;
  public ?PackStation $PackStation;
  public ?string $streetNameCode;
  public ?string $streetNumberCode;
}