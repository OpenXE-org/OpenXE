<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\Go\Data;

class NeutralAddress {
    public string $name1 = '';
    public string $name2 = '';
    public string $name3 = '';
    public string $street = '';
    public string $houseNumber = '';
    public string $zipCode = '';
    public string $city = '';
    public string $country = '';

}
class Address extends NeutralAddress {
    public string $phoneNumber = '';
    public string $remarks = '';
    public string $email = '';
}