<?php

declare(strict_types=1);

namespace Xentral\Modules\Office365Api\Exception;

use Exception;

interface Office365ExceptionInterface extends \Throwable
{
}

abstract class Office365Exception extends Exception implements Office365ExceptionInterface
{
}
