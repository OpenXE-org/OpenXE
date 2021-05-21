<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Interfaces;

use Xentral\Components\Database\Exception\EscapingException;
use Xentral\Components\SchemaCreator\Exception\LineGeneratorException;
use Xentral\Components\SchemaCreator\Exception\SchemaCreatorTableException;
use Xentral\Components\SchemaCreator\Schema\TableSchema;
use Exception;

interface DriverInterface
{

    /**
     * @param TableSchema $currentSchema
     * @param TableSchema $targetSchema
     *
     * @throws EscapingException
     * @throws Exception
     *
     * @return string
     */
    public function generateTableSchemaDiff(TableSchema $currentSchema, TableSchema $targetSchema): string;

    /**
     * @param string $table
     *
     * @throws LineGeneratorException
     * @throws SchemaCreatorTableException
     * @throws Exception
     *
     * @return TableSchema
     */
    public function loadFromTable(string $table): TableSchema;

    /**
     * @param TableSchema $currentSchema
     *
     * @throws EscapingException
     * @throws Exception
     *
     * @return string
     */
    public function getTableDefinition(TableSchema $currentSchema): ?string;
}
