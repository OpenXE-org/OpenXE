<?php

declare(strict_types=1);

namespace Xentral\Modules\Resubmission\Exception;

use RuntimeException;

final class ViewNotFoundException extends RuntimeException implements ResubmissionExceptionInterface
{

}
