<?php

namespace Xentral\Modules\Report\Exception;

use RuntimeException as SplRuntimeException;

class UnresolvedParameterException extends SplRuntimeException implements ReportExceptionInterface
{
}
