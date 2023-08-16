<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Xentral\Components\I18n\Dataaccess;


/**
 * Interface to the general data provider class.
 *
 * @see      DataProvider
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
interface DataProviderInterface
{
    /**
     * Returns an array suitable for select fields.
     * The key of the filtered data set is used as key of the
     * array and $desiredName field is used as value.
     *
     * @param string $desiredName
     *
     * @return array;
     */
    public function getMultiOptions($desiredName): array;
    
    
    
    /**
     * Returns the field $desiredName from the record $id.
     *
     * @param mixed  $id
     * @param string $desiredName
     *
     * @return string
     */
    public function getString($id, $desiredName): string;
    
    
    
    /**
     * Returns the item at position $id.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function getData($id);
}
