<?php

namespace Xentral\Components\Http\Exception;

use RuntimeException;

class CsrfTokenException extends RuntimeException implements HttpComponentExceptionInterface
{
}
