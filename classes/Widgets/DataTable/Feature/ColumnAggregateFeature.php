<?php

namespace Xentral\Widgets\DataTable\Feature;

use Xentral\Widgets\DataTable\Options\DataTableOptions;
use Xentral\Widgets\DataTable\DataTableInterface;
use Xentral\Widgets\DataTable\Exception\FeatureNotImplementedException;

/**
 * @todo
 *
 * @example https://datatables.net/reference/api/columns().footer()#Example
 */
final class ColumnAggregateFeature implements DataTableFeatureInterface
{
    /**
     * @throws FeatureNotImplementedException
     */
    public function __construct()
    {
        throw new FeatureNotImplementedException('Feature is not implemented yet.');
    }

    /**
     * @param DataTableInterface $table
     *
     * @return void
     */
    public function modifyTable(DataTableInterface $table)
    {
        $this->modifyOptions($table->getOptions());
    }

    /**
     * @param DataTableOptions $options
     *
     * @return void
     */
    private function modifyOptions(DataTableOptions $options)
    {
    }
}
