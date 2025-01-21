<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\Go\Data;

use Xentral\Carrier\SendCloud\Data\Label;

class CreateOrderRequest {
    public string $responsibleStation;
    public string $customerId;
    public Shipment $shipment;
    public Address $consignorAddress;
    public NeutralAddress $neutralAddress;
    public Address $consigneeAddress;
    public LabelType $label;
    /***
     * @var Package[]
     */
    public array $packages;

    public function __construct() {
        $this->shipment = new Shipment();
        $this->consignorAddress = new Address();
        $this->consigneeAddress = new Address();
        $this->neutralAddress = new NeutralAddress();
        $this->packages = [new Package()];
    }
}