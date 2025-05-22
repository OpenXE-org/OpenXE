<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\DpdDepot\Data;

class Address
{
    public string $name1 = '';
    public string $name2 = '';
    public string $street = '';
    public ?string $houseNo = null;
    public string $country = '';
    public string $zipCode = '';
    public string $city = '';
    public ?string $customerNumer = null;
    public ?string $contact = null;
    public ?string $phone = null;
    public ?string $mobile = null;
    public ?string $fax = null;
    public ?string $email = null;
    public ?string $comment = null;
}

class AddressWithBusinessUnit extends Address
{
    public ?int $businessUnit = null;
}

class AddressWithType extends AddressWithBusinessUnit
{
    public ?string $addressType = null;
}