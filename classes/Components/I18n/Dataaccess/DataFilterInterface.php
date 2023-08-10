<?php

declare(strict_types=1);

namespace Xentral\Components\I18n\Dataaccess;


/**
 * Filter Interface.
 *
 * @see      DataFilter
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
interface DataFilterInterface
{
    /**
     * Add a filter to the end of the filter chain.
     *
     * @param DataFilterInterface $filter Filter to add
     *
     * @return DataFilterInterface Start of filter chain
     */
    function then(DataFilterInterface $filter): DataFilterInterface;
    
    
    
    /**
     * Applies the filter to the data and executes the next filter
     * if present.
     *
     * @see \Ruga\I18n\Dataaccess\DataFilterInterface::__invoke()
     */
    function __invoke(array $data): array;
}
