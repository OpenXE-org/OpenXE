<?php

namespace Xentral\Widgets\DataTable\Exception;

use RuntimeException;

/**
 * Wenn bereits eine Column mit diesem Name existiert; Namen wüssen einmalig sein
 */
class ColumnNameAssignedException extends RuntimeException implements DataTableExceptionInterface
{
}
