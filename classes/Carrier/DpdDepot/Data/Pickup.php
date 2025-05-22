<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\DpdDepot\Data;

use DateTime;

class Pickup
{
    public ?int $quantity = null;
    /**
     * @var int|null Format is YYYYMMDD. For collection requests, format is YYMMDD.
     */
    public ?int $date = null;
    public ?string $fromTime1 = null;
    public ?string $toTime1 = null;

    public ?Address $collectionRequestAddress = null;

    public function setDate(DateTime $date): void
    {
        $this->date = $date->format('Ymd');
    }

    public function setFromTime(DateTime $fromTime): void
    {
        $this->fromTime1 = $fromTime->format('Hi');
    }

    public function setToTime(DateTime $toTime): void
    {
        $this->toTime1 = $toTime->format('Hi');
    }
}