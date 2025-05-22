<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\DpdDepot\Data;

class ShipmentServiceData
{
    public GeneralShipmentData $generalShipmentData;
    public array $parcels = [];
    public ProductAndServiceData $productAndServiceData;

    public function __construct()
    {
        $this->generalShipmentData = new GeneralShipmentData();
        $this->productAndServiceData = new ProductAndServiceData();
    }


}