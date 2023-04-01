<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Carrier\Dhl\Data;

class Statusinformation
{
  public int $statusCode;
  public string $statusText;
  public array|string $statusMessage;
  public string $statusType;
  public StatusElement $errorMessage;
  public StatusElement $warningMessage;
}