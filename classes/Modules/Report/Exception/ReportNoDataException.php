<?php

namespace Xentral\Modules\Report\Exception;

use RuntimeException as SplRuntimeException;

class ReportNoDataException extends SplRuntimeException implements ReportExceptionInterface
{
}
