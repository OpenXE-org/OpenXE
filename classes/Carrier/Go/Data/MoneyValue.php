<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\Go\Data;

class MoneyValue implements \JsonSerializable {
    public float $amount = 0;
    public string $currency = 'EUR';

    public function jsonSerialize()
    {
        return [
            'amount' => $this->amount > 0 ? (string)$this->amount : '',
            'currency' => $this->amount > 0 ? $this->currency : '',
        ];
    }
}