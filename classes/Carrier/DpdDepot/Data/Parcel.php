<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\DpdDepot\Data;

class Parcel
{
    public ?string $parcelLabelNumber = null;
    public ?string $customerReferenceNumber1 = null;
    public ?string $customerReferenceNumber2 = null;
    public ?string $customerReferenceNumber3 = null;
    public ?string $customerReferenceNumber4 = null;
    public ?bool $swap = null;

    /**
     * @var int|null Volume of the single parcel (length/width/height in format LLLWWWHHH) in cm without separators.
     */
    public ?int $volume = null;


    /**
     * @var int|null Parcel weight in grams rounded in 10 gram units without decimal point (e.g. 300 equals 3kg).
     */
    public ?int $weight = null;
    public ?bool $hazardousLimitedQuantities = null;
    public ?HigherInsurance $higherInsurance = null;
    public ?string $content = null;
    public ?int $addService = null;
    public ?int $messageNumber = null;
    public ?string $function = null;
    public ?string $parameter = null;
    /**
     * @var Hazardous[]
     */
    public array $hazardous = [];
    public ?bool $printInfo1OnParcelLabel = null;
    public ?string $info1 = null;
    public ?string $info2 = null;
    public ?string $returns = null;
    public ?string $parcelClass = null;
}