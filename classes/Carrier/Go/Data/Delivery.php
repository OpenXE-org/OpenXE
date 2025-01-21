<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\Go\Data;

class Delivery implements \JsonSerializable {
    public \DateTime $dateFrom;
    public \DateTime $dateTill;
    public bool $dateIsAvis = false;
    public bool $deliveryOnSaturday = false;
    public bool $deliveryOnHoliday = false;

    public function jsonSerialize()
    {
        return [
            'date' => isset($this->dateFrom) ? $this->dateFrom->format('d.m.Y') : '',
            'timeFrom' => !$this->dateIsAvis && isset($this->dateFrom) ? $this->dateFrom->format('H:i') : '',
            'timeTill' => !$this->dateIsAvis && isset($this->dateTill) ? $this->dateTill->format('H:i') : '',
            'avisFrom' => $this->dateIsAvis && isset($this->dateFrom) ? $this->dateFrom->format('H:i') : '',
            'avisTill' => $this->dateIsAvis && isset($this->dateTill) ? $this->dateTill->format('H:i') : '',
            'weekendOrHolidayIndicator' => $this->deliveryOnHoliday ? 'H' : ($this->deliveryOnSaturday ? 'S' : '')
        ];
    }
}