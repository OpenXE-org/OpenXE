<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\DpdDepot\Data;

enum NotificationChannel: int
{
    case Email = 1;
    case Phone = 2;
    case SMS = 3;
}
