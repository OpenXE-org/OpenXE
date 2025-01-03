<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\Go\Data;

enum OrderStatus : string {
    case New = 'New';
    case Released = 'Released';
    case Cancelled = 'Cancelled';
}