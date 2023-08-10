<?php

declare(strict_types=1);

namespace Xentral\Components\I18n\Dataaccess;

/**
 * Provides countable functions to the data provider.
 *
 * @see      \Countable
 * @see      DataProvider
 * @see      DataProviderInterface
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
trait CountableTrait
{
    /**
     * Counts the number of records in the private $data array.
     *
     * @return int Number of records
     */
    public function count(): int
    {
        return count($this->DataProvider_DATA);
    }
}
