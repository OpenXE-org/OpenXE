<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm\Handler;

use Xentral\Modules\Datanorm\Exception\WrongDiscountFormatException;
use Xentral\Modules\Datanorm\Exception\WrongPriceFormatException;

class AbstractDatanormReaderHandler
{

    /**
     * @param string $price
     *
     * @throws WrongPriceFormatException
     *
     * @return float
     */
    protected function convertPrice(string $price): float
    {
        if (empty($price)) {
            return 0.0;
        }

        if (preg_match('/[^0-9]/', $price)) {
            throw new WrongPriceFormatException('price has a wrong format: ' . $price);
        }

        return (float)$price / 100;
    }

    /**
     * @param string $discountKey
     * @param string $discount
     *
     * @throws WrongDiscountFormatException
     *
     * @return float
     */
    protected function convertDiscount(string $discountKey, string $discount): float
    {
        if (preg_match('/[^0-9]/', $discount)) {
            throw new WrongDiscountFormatException('discount has a wrong format: ' . $discount);
        }

        // Discount
        if ($discountKey === '1') {
            $formatted = (float)$discount / 100;
        } // Factor
        elseif ($discountKey === '2') {
            $formatted = (float)$discount / 1000;
        } // Surcharge
        elseif ($discountKey === '3') {
            $formatted = (float)$discount / 100;
        } else {
            throw new WrongDiscountFormatException(
                'DiscountKey has wrong format. Only 1,2,3 are allowed. ' . $discountKey . ' given'
            );
        }

        return (float)$formatted;
    }
}
