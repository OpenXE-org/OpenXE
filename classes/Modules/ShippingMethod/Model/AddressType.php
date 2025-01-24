<?php

namespace Xentral\Modules\ShippingMethod\Model;

enum AddressType : int {
    case COMPANY = 0;
    case PARCELSTATION = 1;
    case SHOP = 2;
    case PRIVATE = 3;
}