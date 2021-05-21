<?php

namespace Xentral\Modules\Report\Exception;

use RuntimeException as SplRuntimeException;

class EmptyQueryException extends SplRuntimeException implements ReportExceptionInterface
{
}
