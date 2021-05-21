<?php

namespace Xentral\Modules\Voucher\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements VoucherExceptionInterface
{
}
