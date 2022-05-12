<?php

namespace Xentral\Components\Pdf\Exception;

use InvalidArgumentException as SplInvalidArgumentExceptionAlias;

class InvalidArgumentException extends SplInvalidArgumentExceptionAlias implements PdfComponentExceptionInterface
{
}
