<?php

declare(strict_types=1);

namespace Xentral\Components\HttpClient\Exception;

use LogicException;

final class InvalidRequestOptionsException extends LogicException implements HttpClientExceptionInterface
{
}
