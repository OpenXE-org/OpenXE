<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Carrier\Dhl\Data;

class Receiver
{
  public string $name1;
  public ?ReceiverNativeAddress $Address;
  public ?PackStation $Packstation;
  public ?Postfiliale $Postfiliale;
  public ?Communication $Communication;
}