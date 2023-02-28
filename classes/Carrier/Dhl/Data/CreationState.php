<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Carrier\Dhl\Data;

class CreationState
{
    public string $sequenceNumber;
    public ?string $shipmentNumber;
    public ?string $returnShipmentNumber;
    public LabelData $LabelData;
}