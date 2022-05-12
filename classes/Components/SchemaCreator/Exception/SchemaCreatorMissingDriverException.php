<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Exception;
use InvalidArgumentException as SplInvalidArgumentException;
class SchemaCreatorMissingDriverException extends  SplInvalidArgumentException implements SchemaCreatorTableExceptionInterface
{

}
