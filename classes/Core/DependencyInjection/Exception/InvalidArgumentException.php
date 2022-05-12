<?php

namespace Xentral\Core\DependencyInjection\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements ContainerExceptionInterface
{
}
