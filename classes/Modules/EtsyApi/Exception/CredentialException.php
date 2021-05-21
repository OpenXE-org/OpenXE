<?php

namespace Xentral\Modules\EtsyApi\Exception;

use RuntimeException as SplRuntimeException;

class CredentialException extends SplRuntimeException implements EtsyApiExceptionInterface
{
}
