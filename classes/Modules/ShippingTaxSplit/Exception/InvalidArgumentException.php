<?php

namespace Xentral\Modules\ShippingTaxSplit\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements ShippingTaxSplitExceptionInterface
{
}
