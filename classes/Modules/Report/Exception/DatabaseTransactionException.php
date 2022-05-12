<?php

namespace Xentral\Modules\Report\Exception;

use RuntimeException as SplRuntimeException;

class DatabaseTransactionException extends SplRuntimeException implements ReportExceptionInterface
{
}
