<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\DpdDepot\Data;

class GeneralShipmentData
{
    public string $sendingDepot = '';
    public string $product = 'CL';
    public AddressWithType $sender;
    public AddressWithType $recipient;

    public function __construct()
    {
        $this->sender = new AddressWithType();
        $this->recipient = new AddressWithType();
    }


}