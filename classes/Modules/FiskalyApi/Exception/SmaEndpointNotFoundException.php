<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Exception;

use RuntimeException as SplRuntimeException;

class SmaEndpointNotFoundException extends SplRuntimeException implements FiskalyApiExceptionInterface
{

}
