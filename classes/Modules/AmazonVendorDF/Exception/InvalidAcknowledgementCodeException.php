<?php

namespace Xentral\Modules\AmazonVendorDF\Exception;

use Exception;
use Xentral\Modules\AmazonVendorDF\Data\AcknowledgementItem;

class InvalidAcknowledgementCodeException extends Exception
{
    public static function invalidCode(string $code)
    {
        return new static(
            "Invalid acknowledgement code \"{$code}\". Hast to be one of: \n" . implode(
                "\n",
                AcknowledgementItem::AVAILABLE_CODES
            )
        );
    }

    public static function missingCode(string $code)
    {
        return new static(
            'Acknowledgement is not accepted nor rejected. You have to call accept() or reject()'
        );
    }
}
