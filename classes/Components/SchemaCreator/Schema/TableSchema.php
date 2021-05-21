<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Schema;

use Xentral\Components\SchemaCreator\Collection\ColumnCollection;
use Xentral\Components\SchemaCreator\Collection\IndexCollection;
use Xentral\Components\SchemaCreator\Collection\TableOptionCollection;
use Xentral\Components\SchemaCreator\Exception\SchemaCreatorTableException;
use Xentral\Components\SchemaCreator\Interfaces\IndexInterface;
use Xentral\Components\SchemaCreator\Interfaces\ColumnInterface;
use Xentral\Components\SchemaCreator\Option\TableOption;


final class TableSchema
{
    /** @var string $table */
    private $table;

    /** @var ColumnCollection $columnCollection */
    private $columnCollection;

    /** @var IndexCollection $indexCollection */
    private $indexCollection;

    /** @var TableOptionCollection $tableOptionCollection */
    private $tableOptionCollection;

    /**
     * @param string                     $tableName
     * @param ColumnCollection|null      $columnCollection
     * @param IndexCollection|null       $indexCollection
     * @param TableOptionCollection|null $tableOptionCollection
     *
     * @throws SchemaCreatorTableException
     */
    public function __construct(
        string $tableName,
        ?ColumnCollection $columnCollection = null,
        ?IndexCollection $indexCollection = null,
        ?TableOptionCollection $tableOptionCollection = null
    ) {
        $this->table = trim($tableName);

        if (empty($this->table)) {
            throw new SchemaCreatorTableException('Table cannot be empty');
        }

        $this->columnCollection = $columnCollection ?? new ColumnCollection();
        $this->indexCollection = $indexCollection ?? new IndexCollection();
        $this->tableOptionCollection = $tableOptionCollection ?? new TableOptionCollection();
    }

    /**
     * @param ColumnInterface $column
     *
     * @return void
     */
    public function addColumn(ColumnInterface $column): void
    {
        $this->columnCollection->add($column);
    }

    /**
     * @param IndexInterface $index
     *
     * @return void
     */
    public function addIndex(IndexInterface $index): void
    {
        $this->indexCollection->add($index);
    }

    /**
     * @return ColumnCollection|ColumnInterface[]
     */
    public function getColumns(): ColumnCollection
    {
        return $this->columnCollection;
    }

    /**
     * @return IndexCollection|IndexInterface[]
     */
    public function getIndexes(): IndexCollection
    {
        return $this->indexCollection;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $column
     *
     * @return bool
     */
    public function hasColumn(string $column): bool
    {
        return $this->columnCollection->hasColumn($column);
    }

    /**
     * @param string $indexName
     *
     * @return bool
     */
    public function hasIndex(string $indexName): bool
    {
        return $this->indexCollection->hasIndex($indexName);
    }

    /**
     * @param TableOption $option
     */
    public function addOption(TableOption $option): void
    {
        $this->tableOptionCollection->add($option);
    }

    /**
     * @return TableOptionCollection|TableOption[]
     */
    public function getOptions(): TableOptionCollection
    {
        return $this->tableOptionCollection;
    }

    /**
     * @param string $optionName
     *
     * @return bool
     */
    public function hasOption(string $optionName): bool
    {
        return $this->tableOptionCollection->hasOption($optionName);
    }

    /**
     * @param string $field
     *
     * @return ColumnInterface|null
     */
    public function getColumnByName(string $field): ?ColumnInterface
    {
        foreach ($this->getColumns() as $configuredColumn) {
            if ($field === $configuredColumn->getField()) {
                return $configuredColumn;
            }
        }

        return null;
    }

    /**
     * @param string $index
     *
     * @return IndexInterface|null
     */
    public function getIndexByName(string $index): ?IndexInterface
    {
        foreach ($this->getIndexes() as $configuredIndex) {
            if ($index === $configuredIndex->getName()) {
                return $configuredIndex;
            }
        }

        return null;
    }
}
