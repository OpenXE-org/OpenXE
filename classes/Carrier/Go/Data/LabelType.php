<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\Go\Data;

enum LabelType: string {
    case ZPL = '1';
    case PDF_A6 = '2';
    case PDF_A4 = '4';
    case TPCL = '5';
}