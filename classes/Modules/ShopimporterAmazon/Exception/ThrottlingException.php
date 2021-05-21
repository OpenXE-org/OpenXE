<?php

declare(strict_types=1);

namespace Xentral\Modules\ShopimporterAmazon\Exception;

use RuntimeException as SplRuntimeException;

class ThrottlingException extends SplRuntimeException implements ShopimporterAmazonExceptionInterface
{

}
