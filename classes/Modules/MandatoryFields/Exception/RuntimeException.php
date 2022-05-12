<?php

declare(strict_types=1);

namespace Xentral\Modules\MandatoryFields\Exception;

use RuntimeException as SplRuntimeException;

final class RuntimeException extends SplRuntimeException implements MandatoryFieldsExceptionInterface
{
}
