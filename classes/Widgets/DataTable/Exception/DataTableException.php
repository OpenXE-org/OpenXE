<?php

namespace Xentral\Widgets\DataTable\Exception;

use RuntimeException;

class DataTableException extends RuntimeException implements DataTableExceptionInterface
{
    /**
     * @param string $columnName
     *
     * @return DataTableException
     */
    /*public static function columnNotFound($columnName)
    {
        return new self(sprintf('Column "%s" not found.', $columnName));
    }*/
}
