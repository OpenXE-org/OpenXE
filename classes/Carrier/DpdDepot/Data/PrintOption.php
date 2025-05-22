<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\DpdDepot\Data;

class PrintOption
{
    public string $outputFormat = 'PDF';
    public string $paperFormat = 'A6';

    public function setPaperFormat(PaperFormat $paperFormat): void
    {
        $this->paperFormat = $paperFormat->value;
    }
}