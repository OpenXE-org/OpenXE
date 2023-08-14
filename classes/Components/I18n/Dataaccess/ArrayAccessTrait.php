<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Xentral\Components\I18n\Dataaccess;

/**
 * Provides array access functions to the data provider.
 *
 * @see      \ArrayAccess
 * @see      DataProvider
 * @see      DataProviderInterface
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
trait ArrayAccessTrait
{
    /**
     * Whether an offset exists.
     *
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->DataProvider_DATA);
    }
    
    
    
    /**
     * Offset to retrieve.
     *
     * @param string $offset
     *
     * @return array
     */
    public function offsetGet($offset): array
    {
        return $this->DataProvider_DATA[$offset];
    }
    
    
    
    /**
     * Assign a value to the specified offset.
     * No function since data set is read only.
     *
     * @param string $offset
     * @param array  $value
     */
    public function offsetSet($offset, $value)
    {
// $this->DataProvider_DATA[$offset]=$value;
    }
    
    
    
    /**
     * Unset an offset.
     * No function since data set is read only.
     *
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
// unset($this->DataProvider_DATA[$offset]);
    }
}
