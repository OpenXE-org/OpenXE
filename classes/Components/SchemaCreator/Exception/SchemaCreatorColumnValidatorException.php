<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Exception;

use RuntimeException as SplRuntimeException;

class SchemaCreatorColumnValidatorException extends SplRuntimeException implements SchemaCreatorTableExceptionInterface
{

}
