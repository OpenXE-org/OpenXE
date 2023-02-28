<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Carrier\Dhl\Data;

class Dimension
{
  public int $length;
  public int $width;
  public int $height;
  public ?string $unit;
}