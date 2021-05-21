<?php

namespace Xentral\Modules\GoogleApi\Exception;

use RuntimeException;

class NoRefreshTokenException extends RuntimeException implements GoogleApiExceptionInterface
{
}
