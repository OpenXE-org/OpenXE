<?php

namespace Xentral\Modules\SystemConfig\Exception;

use RuntimeException as SplRuntimeException;

class SystemConfigClassCreationFailed extends SplRuntimeException implements SystemConfigExceptionInterface
{
}
