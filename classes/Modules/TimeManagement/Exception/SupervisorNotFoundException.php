<?php

declare(strict_types=1);

namespace Xentral\Modules\TimeManagement\Exception;

use RuntimeException as SplRuntimeException;

final class SupervisorNotFoundException extends SplRuntimeException implements TimeManagementExceptionInterface
{
}
