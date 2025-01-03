<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\Go\Data;

class Shipment implements \JsonSerializable {
    public string $hwbNumber = '';
    public OrderStatus $orderStatus;
    public bool $validation = true;
    public ShipmentService $service;
    public float $weight;
    public string $content = '';
    public string $customerReference = '';
    public ?string $costCenter;
    public bool $selfPickup = false;
    public bool $selfDelivery = false;
    public int $width;
    public int $length;
    public int $height;
    public int $packageCount = 1;
    public bool $freightCollect = false;
    public bool $identCheck = false;
    public bool $receiptNotice = false;
    public bool $isNeutralPickup = false;
    public Pickup $pickup;
    public Delivery $delivery;
    public ?MoneyValue $insurance;
    public ?MoneyValue $valueOfGoods;
    public ?MoneyValue $cashOnDelivery;

    public function __construct()
    {
        $this->pickup = new Pickup();
        $this->delivery = new Delivery();
        $this->insurance = new MoneyValue();
        $this->valueOfGoods = new MoneyValue();
        $this->cashOnDelivery = new MoneyValue();
    }

    public function SetService(string $service) {
        $this->service = ShipmentService::from($service);
    }

    public function jsonSerialize()
    {
        $array = (array) $this;
        $array['dimensions'] = '';
        return array_map(function ($value) {
                if (is_bool($value))
                    return $value ? 'Yes' : 'No';
                return $value;
            }, $array);
    }
}