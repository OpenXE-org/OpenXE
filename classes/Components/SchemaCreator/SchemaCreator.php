<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator;

use Xentral\Components\Database\Database;
use Xentral\Components\Database\Exception\DatabaseExceptionInterface;
use Xentral\Components\Database\Exception\EscapingException;
use Xentral\Components\Database\Exception\TransactionException;
use Xentral\Components\SchemaCreator\Exception\SchemaCreatorTableException;
use Xentral\Components\SchemaCreator\Interfaces\DriverInterface;
use Xentral\Components\SchemaCreator\Schema\TableSchema;

final class SchemaCreator
{

    /** @var Database $db */
    private $db;

    /** @var DriverInterface $driver */
    private $driver;

    /**
     * @param Database        $db
     * @param DriverInterface $driver
     */
    public function __construct(Database $db, DriverInterface $driver)
    {
        $this->db = $db;
        $this->driver = $driver;
    }

    /**
     * @param TableSchema $targetSchema
     *
     * @throws EscapingException
     * @throws Exception\LineGeneratorException
     * @throws SchemaCreatorTableException
     * @throws TransactionException
     *
     * @return void
     */
    public function ensureSchema(TableSchema $targetSchema): void
    {
        $this->applyTableSchema($targetSchema);
    }

    /**
     * @param TableSchema $currentSchema
     * @param TableSchema $targetSchema
     *
     * @throws EscapingException
     *
     * @return string
     */
    public function getDiffSQL(TableSchema $currentSchema, TableSchema $targetSchema): string
    {
        return $this->driver->generateTableSchemaDiff($currentSchema, $targetSchema);
    }

    /**
     * @param string $table
     *
     * @throws Exception\LineGeneratorException
     * @throws SchemaCreatorTableException
     *
     * @return TableSchema
     */
    public function createFromExistingTable(string $table): TableSchema
    {
        return $this->driver->loadFromTable($table);
    }

    /**
     * @param TableSchema $targetSchema
     *
     * @throws EscapingException
     * @throws Exception\LineGeneratorException
     * @throws SchemaCreatorTableException
     * @throws TransactionException
     *
     * @return void
     */
    private function applyTableSchema(TableSchema $targetSchema): void
    {
        $sqlSchema = $this->getSqlSchema($targetSchema);
        $this->applySqlSchema($sqlSchema);
    }

    /**
     * Check whether the table exists
     *
     * @param string $tableName
     *
     * @return bool
     */
    private function hasTable(string $tableName): bool
    {
        $tables = $this->db->fetchCol('SHOW TABLES');

        return in_array($tableName, $tables, true);
    }

    /**
     * @param TableSchema $schema
     *
     * @throws EscapingException
     *
     * @return string
     */
    private function generateSQLDefinition(TableSchema $schema): string
    {
        return $this->driver->getTableDefinition($schema);
    }

    /**
     * @param TableSchema $schema
     *
     * @throws EscapingException
     * @throws Exception\LineGeneratorException
     * @throws SchemaCreatorTableException
     *
     * @return string
     */
    public function getSqlSchema(TableSchema $schema): string
    {
        $table = $schema->getTable();
        if (!$this->hasTable($table)) {
            return $this->generateSQLDefinition($schema);
        }

        $currentSchema = $this->createFromExistingTable($table);
        $diffSQL = $this->getDiffSQL($currentSchema, $schema);

        if (empty($diffSQL)) {
            return '';
        }

        return $diffSQL;
    }

    /**
     * @param string $sql
     *
     * @throws SchemaCreatorTableException
     * @throws TransactionException
     *
     * @return void
     */
    private function applySqlSchema(string $sql): void
    {
        if (empty($sql)) {
            return;
        }
        $this->db->beginTransaction();
        try {
            $this->db->exec($sql);
            $this->db->commit();
        } catch (DatabaseExceptionInterface $e) {
            $this->db->rollBack();
            throw new SchemaCreatorTableException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }
}
