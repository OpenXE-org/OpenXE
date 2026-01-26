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
    
    private $textLengths = [
        'name1' => 35,
        'name2' => 35,
        'street' => 35,
        'houseNo' => 8,
        'state' => 2,
        'country' => 2,
        'zipCode' => 9,
        'city' => 35,
        'customerNumer' => 17,
        'contact' => 35,
        'phone' => 30,
        'mobile' => 30,
        'fax' => 30,
        'email' => 100,
        'comment' => 70,
        'iaccount' => 50
    ];
    
    public function limitTextLengths() {
        foreach ($this->textLengths as $key => $value) {
            if (isset($this->$key) && gettype($this->key == 'string')) {
                $this->$key = substr($this->$key, 0, $value);
            }
        }
    }
}

class AddressWithBusinessUnit extends Address
{
    public ?int $businessUnit = null;
}

class AddressWithType extends AddressWithBusinessUnit
{
    public ?string $addressType = null;
}