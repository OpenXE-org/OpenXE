<?php

namespace Xentral\Modules\ShippingMethod\Model;

class Package {
    public ?int $length;
    public ?int $width;
    public ?int $height;
    public float $weight;
}