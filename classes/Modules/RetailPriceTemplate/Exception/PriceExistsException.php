<?php

namespace Xentral\Modules\RetailPriceTemplate\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class PriceExistsException extends SplInvalidArgumentException implements RetailPriceTemplateExceptionInterface
{
}
