<?php

namespace Xentral\Components\Exporter\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements ExporterExceptionInterface
{
}
