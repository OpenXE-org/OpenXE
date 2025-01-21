<?php

// SPDX-FileCopyrightText: 2024 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Modules\Onlineshop\Data;

class Shipment
{
    /**
     * @var int ID of the shipment (ERP domain)
     */
    public int $id;

    /**
     * @var ?string plain tracking number
     */
    public ?string $trackingNumber;

    /**
     * @var ?string URL to view tracking details
     */
    public ?string $trackingUrl;

    /**
     * @var ?string shipping method (after mapping to Shop domain)
     */
    public ?string $shippingMethod;
}