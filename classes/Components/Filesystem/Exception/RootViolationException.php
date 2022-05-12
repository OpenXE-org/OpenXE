<?php

namespace Xentral\Components\Filesystem\Exception;

use LogicException;

class RootViolationException extends LogicException implements FilesystemExceptionInterface
{
}
