<?php

namespace Xentral\Modules\RetailPriceTemplate\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements RetailPriceTemplateExceptionInterface
{
}
