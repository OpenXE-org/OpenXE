<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Carrier\SendCloud\Data;

/**
 * Error returned during parcel creation
 */
class ParcelCreationError
{
  public int $Code;
  public string $Message;
  public string $Request;
}