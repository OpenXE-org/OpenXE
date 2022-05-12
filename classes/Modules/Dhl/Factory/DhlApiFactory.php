<?php

namespace Xentral\Modules\Dhl\Factory;

use Xentral\Modules\Dhl\Api\DhlApi;

/**
 * Class DhlApiFactory
 *
 * @package Xentral\Modules\Dhl\Factory
 */
class DhlApiFactory
{
    public static function createProductionInstance(
        $user,
        $pass,
        $accountNumber,
        $senderName,
        $senderStreetName,
        $senderStreetNo,
        $senderZip,
        $senderCity,
        $senderCountry,
        $senderEmail
    ) {
        return new DhlApi(
            $user,
            $pass,
            'wawision_2',
            'SQBKcoTz8GgOUp31VNyoZfWooSad3n',
            $accountNumber,
            'https://cig.dhl.de/services/production/soap',
            $senderName,
            $senderStreetName,
            $senderStreetNo,
            $senderZip,
            $senderCity,
            $senderCountry,
            $senderEmail
        );
    }
}
