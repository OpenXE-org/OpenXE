<?php

namespace Xentral\Modules\Ebay\Exception;

use UnexpectedValueException as SplUnexpectedValueException;

class MissingValueException extends SplUnexpectedValueException implements EbayExceptionInterface
{
}
