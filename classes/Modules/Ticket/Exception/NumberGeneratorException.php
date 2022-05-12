<?php

declare(strict_types=1);

namespace Xentral\Modules\Ticket\Exception;

use  \RuntimeException as SplRuntimeException;

class NumberGeneratorException extends SplRuntimeException implements TicketExceptionInterface
{
}
