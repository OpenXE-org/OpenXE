<?php

namespace Xentral\Modules\RetailPriceTemplate\Exception;

use RuntimeException as SplRuntimeException;

class NotFoundException extends SplRuntimeException implements RetailPriceTemplateExceptionInterface
{
}
