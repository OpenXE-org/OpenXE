<?php

declare(strict_types=1);

namespace Xentral\Modules\PaymentMethod\Exception;

use RuntimeException;

final class PaymentMethodNotFoundException extends RuntimeException implements PaymentMethodExceptionInterface
{
}

