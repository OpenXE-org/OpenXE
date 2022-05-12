<?php

namespace Xentral\Widgets\DataTable\Filter;

use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

class FilterCollection implements JsonSerializable, IteratorAggregate
{
    /** @var array|FilterInterface[] $filters */
    protected $filters = [];

    /**
     * @param array|FilterInterface[] $filters
     */
    public function __construct(array $filters = [])
    {
        foreach ($filters as $filter) {
            $this->add($filter);
        }
    }

    /**
     * @param FilterInterface $filter
     *
     * @return void
     */
    public function add(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * @return array|FilterInterface[]
     */
    public function all()
    {
        return $this->filters;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];

        foreach ($this->filters as $filter) {
            $result[] = $filter->toArray();
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
        return new ArrayIterator($this->filters);
    }

    /**
     * Deep copy object
     *
     * @return void
     */
    public function __clone()
    {
        foreach ($this->filters as $index => $filter) {
            $this->filters[$index] = clone $filter;
        }
    }
}
