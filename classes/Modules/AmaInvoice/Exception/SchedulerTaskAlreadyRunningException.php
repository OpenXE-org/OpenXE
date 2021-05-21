<?php

namespace Xentral\Modules\AmaInvoice\Exception;

use RuntimeException;

final class SchedulerTaskAlreadyRunningException extends RuntimeException implements AmaInvoiceExceptionInterface
{
}
