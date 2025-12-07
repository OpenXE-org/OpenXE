<?php
/*
 * SPDX-FileCopyrightText: 2025 Andreas Palm
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

namespace Xentral\Modules\Lieferschwelle\Data;

class Lieferschwelle {
    public function __construct(
        public string $destinationCountryIso,
        public ?string $originCountryIso = null,
        public ?string $ustId = null,
        public bool $active = true,
        public ?int $id = null,
    ) {}
}