<?php

namespace Xentral\Modules\AmazonVendorDF\Exception;

use Exception;

class MissingInformationException extends Exception
{
    public static function property(string $property)
    {
        return new static("\"{$property}\" is not set!");
    }
}
