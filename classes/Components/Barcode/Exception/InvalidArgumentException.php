<?php

namespace Xentral\Components\Barcode\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements BarcodeExceptionInterface
{
}
