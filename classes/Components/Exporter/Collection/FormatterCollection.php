<?php

namespace Xentral\Components\Exporter\Collection;

use ArrayObject;
use Closure;
use Iterator;
use IteratorAggregate;
use Xentral\Components\Exporter\Exception\InvalidArgumentException;
use Xentral\Components\Exporter\Exception\InvalidReturnTypeException;

final class FormatterCollection implements Iterator
{
    /** @var Iterator $data */
    private $data;

    /** @var callable $callback */
    private $callback;

    /**
     * @param array|Iterator   $data
     * @param callable|Closure $callback
     *
     * @throws InvalidArgumentException
     */
    public function __construct($data, $callback)
    {
        if (!is_callable($callback, false)) {
            throw new InvalidArgumentException('Callback is not callable');
        }
        $this->callback = $callback;

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
                $this->data = (new ArrayObject($data))->getIterator();
                break;

            case 'Iterator':
                $this->data = $data;
                break;

            case 'IteratorAggregate':
                $this->data = $data->getIterator();
                break;

            default:
                throw new InvalidArgumentException(sprintf('Unsupported type "%s".', $type));
                break;
        }
    }

    /**
     * Return the current element
     *
     * @throws InvalidReturnTypeException
     *
     * @return mixed Can return any type.
     */
    public function current()
    {
        $result = call_user_func($this->callback, $this->data->current());

        if (!is_array($result)) {
            throw new InvalidReturnTypeException('Formatter return type is invalid . Callable must return an array.');
        }

        return $result;
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
