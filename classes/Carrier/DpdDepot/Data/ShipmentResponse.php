<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\DpdDepot\Data;

class ShipmentResponse
{
    public ?string $identificationNumber = null;
    public ?string $mpsId = null;
    /**
     * @var ParcelInformation[]
     */
    public array $parcelInformation = [];
    public array $faults = [];
}