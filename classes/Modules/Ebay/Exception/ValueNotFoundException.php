<?php

namespace Xentral\Modules\Ebay\Exception;

use RuntimeException as SplRuntimeException;

class ValueNotFoundException extends SplRuntimeException implements EbayExceptionInterface
{
}
