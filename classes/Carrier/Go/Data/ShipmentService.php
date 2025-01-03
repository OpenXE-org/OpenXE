<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\Go\Data;

enum ShipmentService: string {
    case Overnight = 'ON';
    case Worldwide = 'INT';
    case OvernightLetter = 'LET';
    case WorldwideLetter = 'INL';
    case OvernightCodedDelivery = 'ONC';
    case OvernightCodedLetter = 'LEC';
    case DirectShipment = 'DI';
}