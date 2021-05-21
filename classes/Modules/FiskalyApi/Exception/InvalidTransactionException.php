<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Exception;

use InvalidArgumentException;

class InvalidTransactionException extends InvalidArgumentException implements FiskalyApiExceptionInterface
{

}
