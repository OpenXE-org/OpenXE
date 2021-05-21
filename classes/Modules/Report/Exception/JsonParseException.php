<?php

namespace Xentral\Modules\Report\Exception;

use RuntimeException as SplRuntimeException;

class JsonParseException extends SplRuntimeException implements ReportExceptionInterface
{
}
