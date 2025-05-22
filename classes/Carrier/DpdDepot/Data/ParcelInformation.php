<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\DpdDepot\Data;

class ParcelInformation
{
    public ?string $parcelLabelNumber = null;
    public ?string $dpdReference = null;
    public ?Output $output = null;
}