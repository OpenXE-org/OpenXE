<?php

declare(strict_types=1);

namespace Xentral\Modules\ShopimporterAmazon\Exception;

use RuntimeException as SplRuntimeException;

class AmazonBadRequestException extends SplRuntimeException implements ShopimporterAmazonExceptionInterface
{

}
