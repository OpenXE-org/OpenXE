<?php

namespace Xentral\Components\Exporter\Collection;

use ArrayObject;
use AppendIterator;
use Iterator;
use IteratorAggregate;
use Xentral\Components\Exporter\Exception\InvalidArgumentException;

final class DataCollection implements Iterator
{
    /** @var AppendIterator $data */
    private $data;

    /**
     * @param array|Iterator $data
     *
     * @throws InvalidArgumentException
     */
    public function __construct(...$data)
    {
        $this->data = new AppendIterator();
        foreach ($data as $item) {
            $this->append($item);
        }
    }

    /**
     * @param array|Iterator|IteratorAggregate $data
     *
     * @throws InvalidArgumentException
     */
    public function append($data)
    {
        $type = gettype($data);
        if ($type === 'object') {
            $type = get_class($data);
            if ($data instanceof Iterator) {
                $type = 'Iterator';
            }
            if ($data instanceof IteratorAggregate) {
                $type = 'IteratorAggregate';
            }
        }

        switch ($type) {
            case 'array':
                $this->data->append((new ArrayObject($data))->getIterator());
                break;

            case 'Iterator':
                $this->data->append($data);
                break;

            case 'IteratorAggregate':
                $this->data->append($data->getIterator());
                break;

            default:
                throw new InvalidArgumentException(sprintf('Unsupported type "%s".', $type));
                break;
        }
    }

    /**
     * Return the current element
     *
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->data->current();
    }

    /**
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
        $this->data->next();
    }

    /**
     * Return the key of the current element
     *
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->data->key();
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->data->valid();
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void
     */
    public function rewind()
    {
        $this->data->rewind();
    }
}
