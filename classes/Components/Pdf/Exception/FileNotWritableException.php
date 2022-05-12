<?php

namespace Xentral\Components\Pdf\Exception;

use RuntimeException;

class FileNotWritableException extends RuntimeException implements PdfComponentExceptionInterface
{
}
