<?php

namespace Xentral\Modules\ShippingMethod\Model;

class Shipment {
    public array $address;
    public string $productId;
    public array $services = [];
    public string $reference = '';
    public float $insuranceValue = 0;
    public string $content = '';
    public Package $package;
    public CustomsDeclaration $customsDeclaration;

    public function __construct() {
        $this->package  = new Package();
        $this->customsDeclaration = new CustomsDeclaration();
    }

}
