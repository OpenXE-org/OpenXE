<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\DpdDepot\Data;

class Hazardous
{
    public string $identificationUnNo = '';
    public string $identificationClass = '';
    public ?string $classificationCode = null;
    public ?string $packingGroup = null;
    public string $packingCode = '';
    public string $description = '';
    public ?string $subsidiaryRisk = null;
    public ?string $tunnelRestrictionCode = null;
    public float $hazardousWeight = 0;
    public ?float $netWeight = null;
    public int $factor = 0;
    public ?string $notOtherwiseSpecified = null;
}