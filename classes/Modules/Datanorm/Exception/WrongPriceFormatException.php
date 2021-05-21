<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm\Exception;

use InvalidArgumentException;

final class WrongPriceFormatException extends InvalidArgumentException implements DatanormExceptionInterface
{
}
