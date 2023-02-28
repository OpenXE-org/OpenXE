<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Carrier\Dhl\Data;

class Customer
{
  public Name $Name;
  public ?string $vatID;
  public string $EKP;
  public NativeAddress $Address;
  public Contact $Contact;
  public ?Bank $Bank;
  public ?string $note;
}