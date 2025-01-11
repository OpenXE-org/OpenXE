<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\Go\Data;

class Pickup implements \JsonSerializable {
    public \DateTime $dateFrom;
    public \DateTime $dateTill;

    public function jsonSerialize()
    {
        return [
            'date' => $this->dateFrom->format('d.m.Y'),
            'timeFrom' => $this->dateFrom->format('H:i'),
            'timeTill' => $this->dateTill->format('H:i'),
        ];
    }
}