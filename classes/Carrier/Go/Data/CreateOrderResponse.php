<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\Go\Data;

class CreateOrderResponse {
    public string $hwbNumber;
    public OrderStatus $orderStatus;
    public \DateTime $pickupDate;
    public \DateTime $deliveryDate;
    public string $hwbOrPackageLabel;

    public array $barcodes;
}