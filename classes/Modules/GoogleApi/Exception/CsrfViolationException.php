<?php

namespace Xentral\Modules\GoogleApi\Exception;

use RuntimeException;

class CsrfViolationException extends RuntimeException implements GoogleApiExceptionInterface
{
}
