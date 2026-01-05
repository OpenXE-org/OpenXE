<?php
/*
 * SPDX-FileCopyrightText: 2025 Andreas Palm
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

namespace Xentral\Modules\CrossSelling\Data;

enum CrossSellingType : int {
    case SIMILAR = 1;
    case ACCESSORIES = 2;
}
