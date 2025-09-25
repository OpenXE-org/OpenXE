<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\DpdDepot\Data;

class ProductAndServiceData
{
    public string $orderType = 'consignment';
    public ?Pickup $pickup = null;
    public ?ParcelShopDelivery $parcelShopDelivery = null;
}