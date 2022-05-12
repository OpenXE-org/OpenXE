<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Collection;

use ArrayIterator;
use IteratorAggregate;
use Xentral\Components\SchemaCreator\Option\TableOption;

final class TableOptionCollection implements IteratorAggregate
{
    /** @var array|TableOption[] */
    private $values = [];

    /**
     * @param TableOption $option
     */
    public function add(TableOption $option): void
    {
        $this->values[] = $option;
    }

    /**
     * @param string $option
     *
     * @return void
     */
    public function remove(string $option): void
    {
        foreach ($this->values as $key => $tableOption) {
            if ($tableOption->getOption() === $option) {
                unset($this->values[$key]);
            }
        }

        $this->values = array_values($this->values);
    }

    /**
     * @param string $option
     *
     * @return TableOption|null
     */
    public function getTableOption(string $option): ?TableOption
    {
        foreach ($this->values as $tableOption) {
            if ($tableOption->getOption() === $option) {
                return $tableOption;
            }
        }

        return null;
    }

    /**
     * @param string $option
     *
     * @return bool
     */
    public function hasOption(string $option): bool
    {
        foreach ($this->values as $tableOption) {
            if ($tableOption->getOption() === $option) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return ArrayIterator|TableOption[]
     */
    public function getIterator()
    {
        return new ArrayIterator($this->values);
    }
}
