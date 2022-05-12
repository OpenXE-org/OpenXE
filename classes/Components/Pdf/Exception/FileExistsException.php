<?php

namespace Xentral\Components\Pdf\Exception;

use RuntimeException;

class FileExistsException extends RuntimeException implements PdfComponentExceptionInterface
{
}
