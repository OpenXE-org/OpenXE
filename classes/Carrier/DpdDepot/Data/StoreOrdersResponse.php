<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\DpdDepot\Data;

class StoreOrdersResponse {
    public ?Output $output = null;
    /**
     * @var ShipmentResponse[]
     */
    public array $shipmentResponses = [];
}