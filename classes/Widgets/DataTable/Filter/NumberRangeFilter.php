<?php

namespace Xentral\Widgets\DataTable\Filter;

use Xentral\Widgets\DataTable\DataTableInterface;
use Xentral\Widgets\DataTable\Exception\FeatureNotImplementedException;
use Xentral\Widgets\DataTable\Request\DataTableRequest;

/**
 * @deprecated Filter ist nocht nicht fertig
 */
final class NumberRangeFilter extends AbstractFilter
{
    /**
     * @throws FeatureNotImplementedException
     */
    public function __construct()
    {
        throw new FeatureNotImplementedException('Filter type not implemented yet.');
    }

    /**
     * @return string
     */
    public function getType()
    {
        return FilterInterface::TYPE_NUMBER_RANGE;
    }

    /**
     * @param DataTableInterface $table
     * @param DataTableRequest   $request
     *
     * @return void
     */
    public function applyFilter(DataTableInterface $table, DataTableRequest $request)
    {
        // TODO: Implement applyFilter() method.
    }
}
