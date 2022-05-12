<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm\Exception;

use RuntimeException;

final class InvalidLineException extends RuntimeException implements DatanormExceptionInterface
{
}
