<?php

namespace Xentral\Modules\Report\Data;

use ArrayIterator;
use Countable;
use Iterator;
use JsonSerializable;
use Xentral\Modules\Report\Exception\FormDataException;

class ReportParameterCollection implements JsonSerializable, Iterator, Countable
{
    /** @var ArrayIterator $paramIterator */
    private $paramIterator;

    /**
     * @param ReportParameter[] $parameters
     */
    public function __construct($parameters = [])
    {
        $this->paramIterator = new ArrayIterator($parameters);
    }

    /**
     * @param array $formData
     *
     * @return ReportParameterCollection
     */
    public static function fromFormData($formData)
    {
        /** @var ReportParameter[] $parameters */
        $parameters = [];
        foreach ($formData as $item) {
            if (!is_array($item)) {
                throw new FormDataException('Parameter form is wrong format. Array of arrays expected.');
            }
            $param = new ReportParameter(
                $item['varname'],
                $item['default_value'],
                $item['displayname'],
                ReportParameter::parseOptions($item['options']),
                $item['description'],
                ($item['editable'] === 1),
                $item['id'],
                $item['control_type']
            );
            $parameters[] = $param;
        }

        return new self($parameters);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->paramIterator as $param) {
            $array[] = $param->toArray();
        }

        return $array;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     */
    public function jsonSerialize()
    {
        return $this->paramIterator->getArrayCopy();
    }

    /**
     * Return the current element
     *
     * @return ReportParameter
     */
    public function current()
    {
        return $this->paramIterator->current();
    }

    /**
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
        $this->paramIterator->next();
    }

    /**
     * Return the key of the current element
     *
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->paramIterator->key();
    }

    /**
     * Checks if current position is valid
     *
     * @return bool
     */
    public function valid()
    {
        return $this->paramIterator->valid();
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->paramIterator->rewind();
    }

    /**
     * Count elements of an object
     *
     * The return value is cast to an integer.
     */
    public function count()
    {
        return $this->paramIterator->count();
    }
}
