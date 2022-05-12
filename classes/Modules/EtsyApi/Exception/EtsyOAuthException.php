<?php

namespace Xentral\Modules\EtsyApi\Exception;

use RuntimeException as SplRuntimeException;

class EtsyOAuthException extends SplRuntimeException implements EtsyApiExceptionInterface
{
}
