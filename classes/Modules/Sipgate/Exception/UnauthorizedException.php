<?php

namespace Xentral\Modules\Sipgate\Exception;

use RuntimeException;

class UnauthorizedException extends RuntimeException implements SipgateExceptionInterface
{
}
