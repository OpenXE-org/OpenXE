<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\DpdDepot\Data;

class StoreOrders
{
    public ?PrintOptions $printOptions = null;
    /**
     * @var ShipmentServiceData[]
     */
    public array $order = [];
}