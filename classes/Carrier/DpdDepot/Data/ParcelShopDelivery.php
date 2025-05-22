<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\DpdDepot\Data;

class ParcelShopDelivery
{
    public ?int $parcelShopId = null;
    public ?string $parcelShopPudoId = null;
    public Notification $parcelShopNotification;

    public function __construct()
    {
        $this->parcelShopNotification = new Notification();
    }


}