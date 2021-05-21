<?php

namespace Xentral\Widgets\DataTable\Feature;

use Xentral\Widgets\DataTable\DataTableInterface;
use Xentral\Widgets\DataTable\Options\DataTableOptions;
use Xentral\Widgets\DataTable\Exception\FeatureIncompatibleException;

/**
 * @see https://datatables.net/extensions/fixedheader/
 */
final class FixedHeaderFeature implements DataTableFeatureInterface
{
    /**
     * @throws FeatureIncompatibleException
     */
    public function __construct()
    {
        throw new FeatureIncompatibleException('Feature "FixedHeaderFeature" does not work currently.');
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
        /** @see https://datatables.net/reference/option/fixedHeader */
        $options->setOption('fixedHeader', true);
    }
}
