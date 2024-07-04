<?php

// SPDX-FileCopyrightText: 2024 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Modules\Onlineshop\Data;

enum OrderStatus
{
    case Imported;
    case InProgress;
    case Completed;
    case Cancelled;
}
