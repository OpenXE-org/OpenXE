<?php

namespace Xentral\Widgets\DataTable\Column;

use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;
use Traversable;
use Xentral\Widgets\DataTable\Exception\ColumnNameAssignedException;
use Xentral\Widgets\DataTable\Exception\InvalidArgumentException;

class ColumnCollection implements JsonSerializable, IteratorAggregate
{
    /** @var array|Column[] $columns */
    protected $columns = [];

    /**
     * @param array|Column[] $columns
     */
    public function __construct(array $columns = [])
    {
        foreach ($columns as $column) {
            $this->add($column);
        }
    }

    /**
     * @param string $columnName
     *
     * @return bool
     */
    public function has($columnName)
    {
        return $this->getByName($columnName) !== null;
    }

    /**
     * @param Column $column
     *
     * @return void
     */
    public function add(Column $column)
    {
        $this->ensureUniqueColumnName($column->getName());
        $this->columns[] = $column;
    }

    /**
     * @param Column $newColumn
     * @param string $columnNameBefore
     *
     * @return void
     */
    public function addAfter(Column $newColumn, $columnNameBefore)
    {
        $this->ensureUniqueColumnName($newColumn->getName());
        $offset = $this->getColumnIndexByName($columnNameBefore) + 1;

        $columnsBefore = array_slice($this->columns, 0, $offset, false);
        $columnsAfter = array_slice($this->columns, $offset, null, false);

        $this->columns = array_merge($columnsBefore, [$newColumn], $columnsAfter);
    }

    /**
     * @param Column $newColumn
     * @param string $columnNameAfter
     *
     * @return void
     */
    public function addBefore(Column $newColumn, $columnNameAfter)
    {
        $this->ensureUniqueColumnName($newColumn->getName());
        $offset = $this->getColumnIndexByName($columnNameAfter);

        $columnsBefore = array_slice($this->columns, 0, $offset, false);
        $columnsAfter = array_slice($this->columns, $offset, null, false);

        $this->columns = array_merge($columnsBefore, [$newColumn], $columnsAfter);
    }

    /**
     * @param string $columnName
     *
     * @return bool
     */
    public function remove($columnName)
    {
        foreach ($this->columns as $index => $column) {
            if ($column->getName() !== $columnName) {
                unset($this->columns[$index]);
                $this->columns = array_values($this->columns);

                return true;
            }
        }

        return false;
    }

    /**
     * @param string $name
     *
     * @return Column|null
     */
    public function getByName($name)
    {
        foreach ($this->columns as $column) {
            if ($column->getName() === $name) {
                return $column;
            }
        }

        return null;
    }

    /**
     * @param int $index
     *
     * @return Column|null
     */
    public function getByIndex($index)
    {
        return isset($this->columns[(int)$index]) ? $this->columns[(int)$index] : null;
    }

    /**
     * @param string $columnName
     *
     *
     * @return int
     */
    public function getColumnIndexByName($columnName)
    {
        $offset = false;
        $this->columns = array_values($this->columns);
        foreach ($this->columns as $index => $column) {
            if ($column->getName() === $columnName) {
                $offset = $index;
            }
        }

        if ($offset === false) {
            throw new InvalidArgumentException(sprintf('Column name "%s" does not exists.', $columnName));
        }

        return $offset;
    }

    /**
     * @return array|Column[]
     */
    public function all()
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getSearchableDbColumns()
    {
        $searchable = [];

        foreach ($this->columns as $column) {
            if ($column->isSearchable()) {
                $searchable[] = $column->getDbColumn();
            }
        }

        return $searchable;
    }

    /**
     * @return array|callable[] Array with callables, indexed by column name;
     *                          Empty array if there aren't any formatters
     */
    public function getFormatters()
    {
        $formatters = [];

        foreach ($this->columns as $column) {
            $colName = $column->getName();
            $formatter = $column->getFormatter();
            if (!empty($colName) && $formatter !== null) {
                $formatters[$colName] = $formatter;
            }
        }

        return $formatters;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];

        foreach ($this->columns as $column) {
            $result[] = $column->toArray();
        }

        return $result;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return ArrayIterator|Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->columns);
    }

    /**
     * Deep copy object
     *
     * @return void
     */
    public function __clone()
    {
        foreach ($this->columns as $index => $column) {
            $this->columns[$index] = clone $column;
        }
    }

    /**
     * @param string $columnName
     *
     * @throws ColumnNameAssignedException
     *
     * @return void
     */
    protected function ensureUniqueColumnName($columnName)
    {
        if ($this->has($columnName)) {
            throw new ColumnNameAssignedException(sprintf('Column name "%s" is already assigend.', $columnName));
        }
    }
}
