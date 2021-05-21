<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Adapter\Driver;

use Exception;
use Xentral\Components\Database\Exception\EscapingException;
use Xentral\Components\SchemaCreator\Collection\ColumnCollection;
use Xentral\Components\SchemaCreator\Collection\IndexCollection;
use Xentral\Components\SchemaCreator\Collection\TableOptionCollection;
use Xentral\Components\SchemaCreator\Exception\SchemaCreatorInvalidArgumentException;
use Xentral\Components\SchemaCreator\Index\Constraint;
use Xentral\Components\SchemaCreator\Index\Index;
use Xentral\Components\SchemaCreator\Index\Primary;
use Xentral\Components\SchemaCreator\Index\Unique;
use Xentral\Components\SchemaCreator\Interfaces\PrimaryKeyInterface;
use Xentral\Components\SchemaCreator\Interfaces\ColumnInterface;
use Xentral\Components\SchemaCreator\Interfaces\IndexInterface;
use Xentral\Components\SchemaCreator\Interfaces\DriverInterface;
use Xentral\Components\SchemaCreator\LineGenerator\Common\ColumnLineGenerator;
use Xentral\Components\SchemaCreator\LineGenerator\Common\ConstraintLineGenerator;
use Xentral\Components\SchemaCreator\LineGenerator\Common\IndexLineGenerator;
use Xentral\Components\SchemaCreator\LineGenerator\Common\TableOptionsGenerator;
use Xentral\Components\SchemaCreator\Option\TableOption;
use Xentral\Components\SchemaCreator\Schema\TableSchema;

final class MysqlDriver implements DriverInterface
{

    /** @var ColumnLineGenerator $columnGenerator */
    private $columnGenerator;

    /** @var IndexLineGenerator $indexGenerator */
    private $indexGenerator;

    /** @var TableOptionsGenerator $tableOptionsGenerator */
    private $tableOptionsGenerator;

    /** @var ConstraintLineGenerator $constraintGenerator */
    private $constraintGenerator;

    /**
     * @param ColumnLineGenerator     $columnGenerator
     * @param IndexLineGenerator      $indexGenerator
     * @param TableOptionsGenerator   $tableOptionsGenerator
     * @param ConstraintLineGenerator $constraintLineGenerator
     */
    public function __construct(
        ColumnLineGenerator $columnGenerator,
        IndexLineGenerator $indexGenerator,
        TableOptionsGenerator $tableOptionsGenerator,
        ConstraintLineGenerator $constraintLineGenerator
    ) {
        $this->columnGenerator = $columnGenerator;
        $this->indexGenerator = $indexGenerator;
        $this->tableOptionsGenerator = $tableOptionsGenerator;
        $this->constraintGenerator = $constraintLineGenerator;
    }

    /**
     * @param ColumnCollection $columnCollection
     * @param IndexCollection  $indexCollection
     *
     * @throws Exception
     * @return string
     */
    private function generateTableColumns(ColumnCollection $columnCollection, IndexCollection $indexCollection): string
    {
        $columnDefinitions = [];
        $hasAutoIncrement = $columnCollection->hasAutoIncrement();
        $autoIncrementColumn = $columnCollection->getAutoIncrementColumn();
        foreach ($columnCollection as $column) {
            $columnDefinitions[] = $this->columnGenerator->generateLine($column);

            $isFieldAsAutoIncrement = $hasAutoIncrement === true && $autoIncrementColumn->getField(
                ) === $column->getField();

            if ($indexCollection !== null && $isFieldAsAutoIncrement === true && $indexCollection->hasPrimaryKey(
                ) === false) {
                $primaryRef = $column->getField();
                $indexCollection->add(new Primary([$primaryRef]));
                $columnCollection->getIterator()->rewind();
            }
        }

        return implode(",\n", $columnDefinitions);
    }

    /**
     * @param IndexCollection  $indexCollection
     * @param ColumnCollection $targetColumns
     *
     * @throws EscapingException
     *
     * @return string
     */
    private function generateTableIndexes(IndexCollection $indexCollection, ColumnCollection $targetColumns): string
    {
        $asIndexes = [];
        foreach ($indexCollection as $tableIndex) {
            /** @var IndexInterface $tableIndex */
            if ($this->hasIndexReferencesInSchema($tableIndex->getReferences(), $targetColumns)) {
                $asIndexes [] = $this->indexGenerator->generateLine($tableIndex);
            }
        }

        return implode(",\n", $asIndexes);
    }

    /**
     * @param TableOptionCollection $tableOptionCollection
     *
     * @throws EscapingException
     *
     * @return string
     */
    private function generateTableOptions(TableOptionCollection $tableOptionCollection): string
    {
        $tableOptions = [];

        foreach ($tableOptionCollection as $tableOption) {
            /** @var TableOption $tableOption */
            $tableOptions[$tableOption->getOption()] = $tableOption->getValue();
        }

        return $this->tableOptionsGenerator->generate($tableOptions);
    }

    /**
     * @inheritDoc
     */
    public function loadFromTable(string $table): TableSchema
    {
        $tableSchema = new TableSchema($table);
        if ($columns = $this->columnGenerator->fetchColumnsFromDb($table)) {
            foreach ($columns as $column) {
                $type = strtolower($column['type']);
                $type = $type === 'int' ? 'integer' : $type; //Cannot use 'Int' as class name as it is reserved
                $type = $type === 'float' ? 'double' : $type; //Cannot use 'Float' as class name as it is reserved
                $classType = '\\Xentral\\Components\\SchemaCreator\\Type\\' . ucfirst($type);
                if (!class_exists($classType)) {
                    throw new SchemaCreatorInvalidArgumentException(sprintf('%s cannot be found', $classType));
                }

                if (!method_exists($classType, 'fromDBColumn')) {
                    throw new SchemaCreatorInvalidArgumentException(
                        sprintf('Method %s::fromDBColumn not found', $classType)
                    );
                }

                if (is_numeric($column['default']) && $this->hasImplemented($classType, 'NumericTypeInterface')) {
                    $column['default'] = (int)$column['default'];
                }

                if ($this->hasImplemented($classType, 'EnumAndSetInterface')) {
                    $column['references'] = explode(',', $column['references']);
                }

                $callback = call_user_func($classType . '::fromDBColumn', $column);
                $tableSchema->addColumn($callback);
            }
        }

        if ($indexes = $this->indexGenerator->fetchIndexesFromDb($table)) {
            $constraints = $this->constraintGenerator->fetchConstraintsFromDb($table);
            foreach ($indexes as $index) {
                if ($index['name'] === PrimaryKeyInterface::INDEX_NAME) {
                    $tableSchema->addIndex(new Primary($index['columns']));
                } elseif ( ($constraintKey = array_search($index['name'], array_column($constraints, 'name'), true)) !== false) {
                    $tableIndex = new Constraint(
                        $constraints[$constraintKey]['name'],
                        $constraints[$constraintKey]['columns'],
                        $constraints[$constraintKey]['reference_table'],
                        $constraints[$constraintKey]['reference_columns']
                    );
                    $tableSchema->addIndex($tableIndex);
                } else {
                    $tableIndex = $index['unique'] === true ? new Unique($index['columns'], $index['name']) : new Index(
                        $index['columns'], $index['name']
                    );
                    $tableSchema->addIndex($tableIndex);
                }
            }
        }

        if ($options = $this->tableOptionsGenerator->fetchOptionsFromDb($table)) {
            $tableSchema->addOption(TableOption::fromEngine($options['engine']));
            $tableSchema->addOption(TableOption::fromCharset($options['table_charset']));
            $tableSchema->addOption(TableOption::fromCollation($options['collation']));
            $tableSchema->addOption(TableOption::fromComment($options['comment']));
        }

        return $tableSchema;
    }

    /**
     * @param string $class
     * @param string $needleInterface
     *
     * @return bool
     */
    private function hasImplemented(string $class, string $needleInterface): bool
    {
        if ($interfaces = class_implements($class)) {
            foreach ($interfaces as $interface) {
                $interface_exploded = explode('\\', $interface);
                $name = array_pop($interface_exploded);
                if ($name === $needleInterface) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param array            $references
     * @param ColumnCollection $targetColumns
     *
     * @return bool
     */
    private function hasIndexReferencesInSchema(array $references, ColumnCollection $targetColumns): bool
    {
        $columns = [];
        foreach ($targetColumns as $configuredColumns) {
            $columns[] = $configuredColumns->getField();
        }

        foreach ($references as $reference) {
            if (!in_array(sprintf('%s', $reference), $columns, true)) {
                throw new SchemaCreatorInvalidArgumentException(
                    sprintf('Column name `%s` missing in the table', $reference)
                );
            }
        }

        return true;
    }

    /**
     * @param array $targetSchema
     * @param array $currentSchema
     *
     * @return bool
     */
    private function checkColumnNeedsUpdate(array $targetSchema, array $currentSchema): bool
    {
        $diff = array_diff_assoc($targetSchema, $currentSchema);

        return count($diff) > 0;
    }

    /**
     * @param TableSchema $currentSchema
     * @param TableSchema $targetSchema
     *
     * @throws EscapingException
     *
     * @return string
     */
    private function generateColumnsDiff(TableSchema $currentSchema, TableSchema $targetSchema): string
    {
        $alterColumnParts = [];
        $targetColumns = $targetSchema->getColumns();
        $currentColumns = $currentSchema->getColumns();
        $currentIndexes = $currentSchema->getIndexes();

        $hasAutoIncrement = $currentColumns->hasAutoIncrement();
        $autoIncrementColumn = $currentColumns->getAutoIncrementColumn();

        foreach ($currentColumns as $schemaKey => $currentColumn) {
            $isFieldAsAutoIncrement = $hasAutoIncrement === true && $autoIncrementColumn->getField(
                ) === $currentColumn->getField();

            if ($currentIndexes !== null && $isFieldAsAutoIncrement === true && $currentIndexes->hasPrimaryKey(
                ) === false) {
                $primaryReference = $currentColumn->getField();
                $currentIndexes->add(new Primary([$primaryReference]));
            }
        }

        $targetFields = $targetColumns->getFields();
        $currentFields = $currentColumns->getFields();

        $newFields = array_diff($targetFields, $currentFields);

        foreach ($targetColumns as $schemaKey => $schemaColumn) {
            /** @var  ColumnInterface $schemaColumn */

            $field = $schemaColumn->getField();

            if (in_array($field, $newFields, true)) {
                $alterColumnParts[] = sprintf('ADD %s', $this->columnGenerator->generateLine($schemaColumn));
            } else {
                $needsUpdate = $this->checkColumnNeedsUpdate(
                    $this->columnGenerator->toArray($schemaColumn),
                    $this->columnGenerator->toArray($currentSchema->getColumnByName($field))
                );
                if ($needsUpdate) {
                    $alterColumnParts[] = sprintf('MODIFY %s', $this->columnGenerator->generateLine($schemaColumn));
                }
            }
        }

        return implode(', ', $alterColumnParts);
    }

    /**
     * @param IndexInterface $targetIndex
     * @param IndexInterface $currentIndex
     *
     * @return bool
     */
    private function checkIndexNeedsUpdate(IndexInterface $targetIndex, IndexInterface $currentIndex): bool
    {
        $targetReferences = $targetIndex->getReferences();
        $currentReferences = $currentIndex->getReferences();

        if ($targetIndex->isUnique() !== $currentIndex->isUnique()) {
            return true;
        }

        $diffColumns = array_diff($targetReferences, $currentReferences);

        return count($diffColumns) > 0;
    }

    /**
     * @param TableSchema $currentSchema
     * @param TableSchema $targetSchema
     *
     * @throws Exception
     * @return string
     */
    private function generateIndexesDiff(TableSchema $currentSchema, TableSchema $targetSchema): string
    {
        $alterIndexParts = [];

        $targetIndexes = $targetSchema->getIndexes();
        $currentIndexes = $currentSchema->getIndexes();
        $targetColumns = $targetSchema->getColumns();

        // FIX MISSING PRIMARY KEY HERE
        $this->generateTableColumns($targetColumns, $targetIndexes);

        $targetIndexNames = $targetIndexes->getIndexNames();
        $currentIndexNames = $currentIndexes->getIndexNames();

        $newIndexes = array_diff($targetIndexNames, $currentIndexNames);
        $removedIndexes = array_diff($currentIndexNames, $targetIndexNames);

        if (count($removedIndexes) > 0) {
            foreach ($removedIndexes as $removedIndexName) {
                if ($removedIndexName !== 'PRIMARY' && !in_array($removedIndexName, $newIndexes, true)) {
                    foreach($currentIndexes as $currentIndex) {
                        if($currentIndex->getName() !== $removedIndexName) {
                            continue;
                        }
                        if($currentIndex->getType() === 'CONSTRAINT') {
                            $alterIndexParts[] = sprintf('DROP FOREIGN KEY %s', $this->indexGenerator->escape($removedIndexName));
                            break;
                        }
                        else {
                            $alterIndexParts[] = sprintf('DROP INDEX %s', $this->indexGenerator->escape($removedIndexName));
                            break;
                        }
                    }

                }
            }
        }

        foreach ($targetIndexes as $schemaKey => $schemaIndex) {
            $indexName = $schemaIndex->getName();
            $indexType = $schemaIndex->getType();
            if (in_array($indexName, $newIndexes, true) &&
                $this->hasIndexReferencesInSchema($schemaIndex->getReferences(), $targetColumns) === true) {
                $alterIndexParts[] = sprintf('ADD %s', $this->indexGenerator->generateLine($schemaIndex));
            } else {
                $needsUpdate = $this->checkIndexNeedsUpdate($schemaIndex, $currentSchema->getIndexByName($indexName));
                if ($needsUpdate) {
                    if ($schemaIndex instanceof PrimaryKeyInterface) {
                        $references = $schemaIndex->getReferences();
                        // CHECK IF one of the reference has AutoIncrement
                        $currentPrimary = $currentSchema->getIndexByName('PRIMARY');
                        if (null !== $currentPrimary) {
                            $currentReferences = $currentPrimary->getReferences();
                            $columnModified = null;
                            foreach ($currentReferences as $columnName) {
                                $column = $currentSchema->getColumnByName($columnName);

                                $options = $column->getOptions();
                                if (array_key_exists('extra', $options) && in_array(
                                        $options['extra'],
                                        ['ai', 'AUTO_INCREMENT']
                                    )) {
                                    $columnModified = sprintf(
                                        'MODIFY %s',
                                        $this->columnGenerator->generateLine($column)
                                    );
                                    $columnModifiedWithoutAi = str_replace('AUTO_INCREMENT', '', $columnModified);
                                    $alterIndexParts[] = trim($columnModifiedWithoutAi);
                                    break;
                                }
                            }

                            $alterIndexParts[] = 'DROP PRIMARY KEY';
                        }

                        $reference = implode(
                            ',',
                            array_map(
                                function ($reference) {
                                    return $this->indexGenerator->escape($reference);
                                },
                                $references
                            )
                        );
                        $alterIndexParts[] = sprintf('ADD PRIMARY KEY (%s)', $reference);
                    } else {
                        if($indexType === 'CONSTRAINT') {
                            $alterIndexParts[] = sprintf('DROP FOREIGN KEY %s', $this->indexGenerator->escape($indexName));
                        }
                        else {
                            $alterIndexParts[] = sprintf('DROP INDEX %s', $this->indexGenerator->escape($indexName));
                        }
                        $alterIndexParts[] = sprintf('ADD %s', $this->indexGenerator->generateLine($schemaIndex));
                    }
                }
            }
        }

        return implode(', ', $alterIndexParts);
    }

    /**
     * @param TableSchema $currentSchema
     * @param TableSchema $targetSchema
     *
     * @throws Exception
     * @return string
     */
    private function generateOptionsDiff(TableSchema $currentSchema, TableSchema $targetSchema): string
    {
        $targetOptions = $targetSchema->getOptions();

        if ($targetOptions->getIterator()->count() === 0) {
            return '';
        }

        $currentOptions = $currentSchema->getOptions();
        $sqlOptions = [];
        $charset = null;
        foreach ($targetOptions as $option) {
            $currentTableOption = $currentOptions->getTableOption($option->getOption());
            if ($currentTableOption === null || $currentTableOption->getValue() !== $option->getValue()) {
                if ($option->getOption() === 'collation' && ($charsetOption = $currentOptions->getTableOption(
                        'charset'
                    ))) {
                    $charset = $charsetOption->getValue();
                }

                $sqlOptions[] = $this->tableOptionsGenerator->alterOptions($option, $charset);
            }
        }

        return implode(' ', $sqlOptions);
    }

    /**
     * @inheritDoc
     */
    public function getTableDefinition(TableSchema $schema): string
    {
        $name = $schema->getTable();
        $columns = $this->generateTableColumns(
            $schema->getColumns(),
            $schema->getIndexes()
        );
        $keys = $this->generateTableIndexes($schema->getIndexes(), $schema->getColumns());

        $columns = rtrim($columns, ',');
        if (!empty($keys)) {
            $keys = ',' . PHP_EOL . $keys;
        }

        $definition = sprintf("(\n%s\n)", $columns . $keys);
        $defFooter = $this->generateTableOptions($schema->getOptions());

        $definition .= $defFooter;


        return sprintf('CREATE TABLE IF NOT EXISTS %s %s', $this->indexGenerator->escape($name), $definition);
    }

    /**
     * @param string $table
     *
     * @throws EscapingException
     *
     * @return string
     */
    private function generateAlterTable(string $table): string
    {
        return sprintf('ALTER TABLE %s ', $this->indexGenerator->escape($table));
    }

    /**
     * @inheritDoc
     */
    public function generateTableSchemaDiff(TableSchema $currentSchema, TableSchema $targetSchema): string
    {
        $sql = '';

        $alterColumns = $this->generateColumnsDiff($currentSchema, $targetSchema);
        $alterIndexes = $this->generateIndexesDiff($currentSchema, $targetSchema);
        $alterOptions = $this->generateOptionsDiff($currentSchema, $targetSchema);

        if (empty($alterColumns) && empty($alterIndexes) && empty($alterOptions)) {
            return $sql;
        }

        if (!empty($alterColumns)) {
            $sql .= $alterColumns . ', ';
        }
        if (!empty($alterIndexes)) {
            $sql .= $alterIndexes . ', ';
        }

        $table = $targetSchema->getTable();
        $alterTable = $this->generateAlterTable($table);
        if (!empty($alterOptions)) {
            $alterTable .= $alterOptions;
            if (!empty($sql)) {
                $alterTable .= ', ';
            }
        }

        $sql = substr_replace($sql, ';', -2);

        return $alterTable . $sql;
    }

}
