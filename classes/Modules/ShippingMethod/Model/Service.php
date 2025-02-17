<?php

namespace Xentral\Modules\ShippingMethod\Model;

enum Service : string {
    case SERVICE_COD = 'cod';
    case SERVICE_PREMIUM = 'premium';
    case SERVICE_PICKUP = 'pickup';
    case SERVICE_PICKUP_DATE = 'pickup_date';
    case SERVICE_PICKUP_TIME = 'pickup_time';
    case SERVICE_HAZMAT = 'hazmat';
}