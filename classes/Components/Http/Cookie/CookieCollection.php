<?php

namespace Xentral\Components\Http\Cookie;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Iterator;

class CookieCollection implements Iterator, Countable, ArrayAccess
{
    /** @var Cookie[] $cookies */
    private $cookies;

    /**
     * CookieCollection constructor.
     *
     * @param Cookie[] $cookies
     */
    public function __construct($cookies = [])
    {
        $this->cookies = new ArrayIterator($cookies);
    }

    /**
     * Returns http headers to be used in response
     *
     * @return string[]
     */
    public function toHttpHeaders()
    {
        $result = [];
        foreach ($this->cookies as $cookie) {
            $result[] = $cookie->toHttpHeader();
        }

        return $result;
    }

    /**
     * @return Cookie
     */
    public function current()
    {
        return $this->cookies->current();
    }

    /**
     * @return void
     */
    public function next()
    {
        $this->cookies->next();
    }

    /**
     * @return int|string
     */
    public function key()
    {
        return $this->cookies->key();
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->cookies->valid();
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->cookies->rewind();
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->cookies->count();
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->cookies);
    }

    /**
     * @param mixed $offset
     *
     * @return Cookie
     */
    public function offsetGet($offset)
    {
        return $this->cookies[$offset];
    }

    /**
     * @param mixed $offset
     * @param Cookie $value
     */
    public function offsetSet($offset, $value)
    {
        $this->cookies[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->cookies[$offset]);
    }
}
