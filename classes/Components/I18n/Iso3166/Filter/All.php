<?php
/**
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Xentral\Components\I18n\Iso3166\Filter;

use Xentral\Components\I18n\Dataaccess\DataFilter;
use Xentral\Components\I18n\Dataaccess\DataFilterInterface;

/**
 * This filter returns all records from the data set (aka dummy filter).
 *
 * @see      \Xentral\Components\I18n\Dataaccess\DataFilter
 * @see      \Xentral\Components\I18n\Dataaccess\DataFilterInterface
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class All extends DataFilter implements DataFilterInterface
{
    /**
     * {@inheritDoc}
     * @see \Xentral\Components\I18n\Dataaccess\DataFilterInterface::selectItem()
     */
    function selectItem(&$key, &$val): bool
    {
        return true;
    }
}
