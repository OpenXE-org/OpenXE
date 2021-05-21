<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm\Exception;

use RuntimeException;

class InvalidArgumentException extends RuntimeException implements DatanormExceptionInterface
{
}
