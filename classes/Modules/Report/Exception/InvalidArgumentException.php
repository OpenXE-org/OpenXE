<?php

namespace Xentral\Modules\Report\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements ReportExceptionInterface
{
}
