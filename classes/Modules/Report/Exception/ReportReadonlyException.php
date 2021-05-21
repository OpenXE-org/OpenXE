<?php

namespace Xentral\Modules\Report\Exception;

use RuntimeException as SplRuntimeException;

class ReportReadonlyException extends SplRuntimeException implements ReportExceptionInterface
{
}
