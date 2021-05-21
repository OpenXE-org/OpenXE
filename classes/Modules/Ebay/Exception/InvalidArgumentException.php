<?php

namespace Xentral\Modules\Ebay\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements EbayExceptionInterface
{
}
