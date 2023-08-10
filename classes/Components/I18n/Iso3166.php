<?php

declare(strict_types=1);

namespace Xentral\Components\I18n;

use Xentral\Components\I18n\Dataaccess\DataProvider;

/**
 * Country Codes - ISO 3166.
 * Loads the data and holds the filtered (if desired) list.
 *
 * @see      https://www.iso.org/iso-3166-country-codes.html
 * @see      DataProvider
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class Iso3166 extends DataProvider
{
    /**
     * {@inheritDoc}
     * @see \Xentral\Components\I18n\Dataaccess\DataProvider::getOriginalData()
     */
    protected function getOriginalData(): array
    {
        return include(__DIR__ . '/data/Iso3166data.php');
    }
}
