<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Carrier\SendCloud\Data;

class ShippingMethod {
    public int $Id;
    public string $Name;
    public ?string $Carrier;
    public int $MinWeight;
    public int $MaxWeight;
    public int $MaxLength;
    public int $MaxWidth;
    public int $MaxHeight;
    public string $Unit;

    public static function fromApiResponse(object $data):ShippingMethod {
        $obj = new ShippingMethod();
        $obj->Id = $data->id;
        $obj->Name = $data->name;
        $obj->Carrier = $data->carrier ?? null;
        $obj->MinWeight = $data->properties->min_weight;
        $obj->MaxWeight = $data->properties->max_weight;
        $obj->MaxLength = $data->properties->max_dimensions->length;
        $obj->MaxWidth = $data->properties->max_dimensions->width;
        $obj->MaxHeight = $data->properties->max_dimensions->height;
        $obj->Unit = $data->properties->max_dimensions->unit;
        return $obj;
    }
}