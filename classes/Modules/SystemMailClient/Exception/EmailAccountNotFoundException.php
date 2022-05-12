<?php

namespace Xentral\Modules\SystemMailClient\Exception;

use RuntimeException;

class EmailAccountNotFoundException extends RuntimeException implements SystemMailClientExceptionInterface
{
}
