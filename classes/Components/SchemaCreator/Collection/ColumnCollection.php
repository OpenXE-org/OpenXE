<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Collection;

use ArrayIterator;
use IteratorAggregate;
use Xentral\Components\SchemaCreator\Exception\SchemaCreatorInvalidArgumentException;
use Xentral\Components\SchemaCreator\Interfaces\ColumnInterface;

final class ColumnCollection implements IteratorAggregate
{

    /** @var array|ColumnInterface[] */
    private $values = [];

    /** @var null|ColumnInterface */
    private $autoIncrementColumn;

    /**
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        foreach ($values as $value) {
            $this->add($value);
        }
    }

    /**
     * @param ColumnInterface $field
     *
     * @return void
     */
    public function add(ColumnInterface $field): void
    {
        if ($this->hasColumn($field->getField()) === true) {
            throw new SchemaCreatorInvalidArgumentException(
                sprintf('Column name `%s` already added', $field->getField())
            );
        }

        $options = $field->getOptions();
        if (array_key_exists('extra', $options) && $options['extra'] !== null) {

             if ($options['extra'] === 'AUTO_INCREMENT' || $options['extra'] === 'ai') {
                 $this->autoIncrementColumn = $field;
             }
        }

        $this->values[] = $field;
    }

    /**
     * @param string $column
     *
     * @return bool
     */
    public function hasColumn(string $column): bool
    {
        /** @var  ColumnInterface $configuredColumns */
        foreach ($this->values as $configuredColumns) {
            if ($column === $configuredColumns->getField()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array|string[]
     */
    public function getFields(): array
    {
        $fields = [];
        foreach ($this->values as $column) {
            $fields[] = $column->getField();
        }

        return $fields;
    }

    /**
     * @return ArrayIterator|ColumnInterface[]
     */
    public function getIterator()
    {
        return new ArrayIterator($this->values);
    }

    /***
     * @return bool
     */
    public function hasAutoIncrement(): bool
    {
        return null !== $this->autoIncrementColumn;
    }

    /**
     * @return ColumnInterface|null
     */
    public function getAutoIncrementColumn(): ?ColumnInterface
    {
        return $this->autoIncrementColumn;
    }


}
