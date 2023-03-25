<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Carrier\Dhl\Data;

class Contact
{
  public ?Communication $Communication;
  public ?NativeAddress $Address;
  public ?Name $Name;
}