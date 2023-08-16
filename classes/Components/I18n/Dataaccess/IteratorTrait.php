<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Xentral\Components\I18n\Dataaccess;

/**
 * Provides iterator functions to the data provider.
 *
 * @see      \Iterator
 * @see      DataProvider
 * @see      DataProviderInterface
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
trait IteratorTrait
{
    /**
     * Index of the current element's key.
     *
     * @var string
     */
    private $IteratorTrait_index = null;
    
    /**
     * Array of keys of the data set.
     *
     * @var array
     */
    private $IteratorTrait_keys = null;
    
    
    
    /**
     * Return the current element.
     *
     * @return mixed
     */
    public function current()
    {
        return $this->valid() ? $this->DataProvider_DATA[$this->key()] : null;
    }
    
    
    
    /**
     * Return the key of the current element.
     *
     * @return mixed
     */
    public function key()
    {
        return static::valid() ? $this->IteratorTrait_keys[$this->IteratorTrait_index] : null;
    }
    
    
    
    /**
     * Move forward to next element.
     *
     * @return bool false if invalid
     */
    public function next()
    {
        $this->IteratorTrait_index++;
        if (!$this->valid()) {
            $this->IteratorTrait_index = null;
        }
        return $this->valid();
    }
    
    
    
    /**
     * Rewind the Iterator to the first element.
     */
    public function rewind()
    {
        $this->IteratorTrait_keys = array_keys($this->DataProvider_DATA);
        sort($this->IteratorTrait_keys);
        $this->IteratorTrait_index = 0;
    }
    
    
    
    /**
     * Checks if current position is valid.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return ($this->IteratorTrait_index !== null)
            && array_key_exists($this->IteratorTrait_index, $this->IteratorTrait_keys)
            && array_key_exists($this->IteratorTrait_keys[$this->IteratorTrait_index], $this->DataProvider_DATA);
    }
}
