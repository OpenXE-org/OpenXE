<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Carrier\Dhl\Data;

class Shipper
{
  public Name $Name;
  public NativeAddressNew $Address;
  public ?Communication $Communication;

  public function __construct()
  {
    $this->Name = new Name();
    $this->Address = new NativeAddressNew();
  }
}