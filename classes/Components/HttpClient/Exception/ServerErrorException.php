<?php

declare(strict_types=1);

namespace Xentral\Components\HttpClient\Exception;

/**
 * Exception for 5xx HTTP status errors
 */
class ServerErrorException extends TransferErrorException
{
}
