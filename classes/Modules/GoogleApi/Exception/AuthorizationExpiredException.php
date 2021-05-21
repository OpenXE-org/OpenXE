<?php

namespace Xentral\Modules\GoogleApi\Exception;

use RuntimeException;

class AuthorizationExpiredException extends RuntimeException implements GoogleApiExceptionInterface
{
}
