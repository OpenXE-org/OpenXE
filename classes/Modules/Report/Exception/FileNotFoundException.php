<?php

namespace Xentral\Modules\Report\Exception;

use RuntimeException as SplRuntimeException;

class FileNotFoundException extends SplRuntimeException implements ReportExceptionInterface
{
}
