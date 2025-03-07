<?php

namespace Xentral\Modules\ShippingMethod\Model;

enum ShipmentType: int {
    case GIFT = 0;
    case DOCUMENTS = 1;
    case GOODS = 2;
    case SAMPLE = 3;
    case RETURN = 4;
}