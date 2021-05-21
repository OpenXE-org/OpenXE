<?php

namespace Xentral\Modules\GoogleApi\Exception;

use RuntimeException;

class NoAccessTokenException extends RuntimeException implements GoogleApiExceptionInterface
{
}
