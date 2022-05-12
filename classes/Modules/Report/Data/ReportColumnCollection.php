<?php

namespace Xentral\Modules\Report\Data;

use ArrayIterator;
use Iterator;
use JsonSerializable;
use Xentral\Modules\Report\Exception\FormDataException;

class ReportColumnCollection implements JsonSerializable, Iterator
{
    /** @var ArrayIterator $columnIterator */
    private $columnIterator;

    /**
     * @param ReportColumn[] $columns
     */
    public function __construct($columns = [])
    {
        $this->columnIterator = new ArrayIterator($columns);
    }

    /**
     * @param array $formData
     *
     * @return ReportColumnCollection
     */
    public static function fromFormData($formData)
    {
        $columns = [];
        foreach ($formData as $item) {
            if (!is_array($item)) {
                throw new FormDataException('Columns form is wrong format. Array of arrays expected.');
            }
            $col = new ReportColumn(
                $item['key_name'],
                $item['title'],
                $item['width'],
                $item['alignment'],
                (int)$item['sum'] === 1,
                (int)$item['id'],
                (int)$item['sequence'],
                $item['sorting']
            );
            $columns[] = $col;
        }

        return new self($columns);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->columnIterator->getArrayCopy();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->columnIterator as $column) {
            $array[] = $column->toArray();
        }

        return $array;
    }

    /**
     * Return the current element
     *
     * @return ReportColumn
     */
    public function current()
    {
        return $this->columnIterator->current();
    }

    /**
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
        $this->columnIterator->next();
    }

    /**
     * Return the key of the current element
     *
     * @return int|null
     */
    public function key()
    {
        return $this->columnIterator->key();
    }

    /**
     * Checks if current position is valid
     *
     * @return bool
     */
    public function valid()
    {
        return $this->columnIterator->valid();
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void
     */
    public function rewind()
    {
        $this->columnIterator->rewind();
    }
}
