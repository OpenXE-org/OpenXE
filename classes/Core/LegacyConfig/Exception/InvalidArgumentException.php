<?php

namespace Xentral\Core\LegacyConfig\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements LegacyConfigExceptionInterface
{
}
