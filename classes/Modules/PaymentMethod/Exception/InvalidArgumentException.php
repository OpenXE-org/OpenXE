<?php

declare(strict_types=1);

namespace Xentral\Modules\PaymentMethod\Exception;

use LogicException;

final class InvalidArgumentException extends LogicException implements PaymentMethodExceptionInterface
{
}

