<?php

// SPDX-FileCopyrightText: 2024 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Modules\ShippingMethod\Model;

enum ShipmentStatus
{
    case Announced;
    case EnRoute;
    case Delivered;
}