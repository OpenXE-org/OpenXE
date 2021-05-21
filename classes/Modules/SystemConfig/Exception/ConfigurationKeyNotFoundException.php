<?php

namespace Xentral\Modules\SystemConfig\Exception;

use RuntimeException as SplRuntimeException;

class ConfigurationKeyNotFoundException extends SplRuntimeException implements SystemConfigExceptionInterface
{
}
