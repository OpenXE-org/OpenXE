<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Exception;

use RuntimeException;
class PipedriveContactGatewayNotFoundException extends RuntimeException
    implements PipedriveExceptionInterface
{

}
