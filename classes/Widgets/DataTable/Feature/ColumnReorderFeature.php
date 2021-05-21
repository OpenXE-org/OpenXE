<?php

namespace Xentral\Widgets\DataTable\Feature;

use Xentral\Widgets\DataTable\Options\DataTableOptions;
use Xentral\Widgets\DataTable\DataTableInterface;
use Xentral\Widgets\DataTable\Exception\FeatureIncompatibleException;

/**
 * @deprecated Nicht verwenden; Momentan inkompatibel mit ColumnFilter! Filter-Eingabefelder werden falsch zugeordnet.
 *
 * @see https://datatables.net/extensions/colreorder/
 */
final class ColumnReorderFeature implements DataTableFeatureInterface
{
    /**
     * @throws FeatureIncompatibleException
     */
    public function __construct()
    {
        throw new FeatureIncompatibleException('DataTable feature "ColumnReorder" is incompatible.');
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
        /** @see https://datatables.net/reference/option/colReorder */
        $options->setOption('colReorder', ['enable' => true, 'realtime' => false]);
    }
}
