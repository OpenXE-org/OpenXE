<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\Go\Data;

class Package implements \JsonSerializable {
    public int $length = 0;
    public int $width = 0;
    public int $height = 0;

    public function jsonSerialize()
    {
        return [
            'length' => $this->length ? (string) $this->length : '',
            'width' => $this->width ? (string) $this->width : '',
            'height' => $this->height ? (string) $this->height : '',
        ];
    }
}