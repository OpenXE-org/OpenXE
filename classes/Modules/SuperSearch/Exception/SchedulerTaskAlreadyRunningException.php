<?php

namespace Xentral\Modules\SuperSearch\Exception;

use RuntimeException;

final class SchedulerTaskAlreadyRunningException extends RuntimeException implements SuperSearchExceptionInterface
{
}
