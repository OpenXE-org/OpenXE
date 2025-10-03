<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\Go\Data;

class Pickup implements \JsonSerializable {
    public \DateTime $date;
    public \DateTime $from;
    public \DateTime $till;

    public function jsonSerialize()
    {
        return [
            'date' => $this->date->format('d.m.Y'),
            'timeFrom' => $this->from->format('H:i'),
            'timeTill' => $this->till->format('H:i'),
        ];
    }
}