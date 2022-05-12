<?php

namespace Xentral\Components\Template\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements TemplateExceptionInterface
{
}
