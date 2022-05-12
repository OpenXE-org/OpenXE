<?php

namespace Xentral\Modules\DatevApi\DataTable;

use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Widgets\DataTable\Column\Column;
use Xentral\Widgets\DataTable\Column\ColumnCollection;
use Xentral\Widgets\DataTable\Feature\FeatureCollection;
use Xentral\Widgets\DataTable\Feature\StateSaveFeature;
use Xentral\Widgets\DataTable\Options\DataTableOptions;
use Xentral\Widgets\DataTable\Type\AbstractDataTableType;

class DatevExportDataTable extends AbstractDataTableType
{
    /**
     * @param DataTableOptions $options
     *
     * @return void
     */
    public function configureOptions(DataTableOptions $options)
    {
        $options->setDefaultSorting(['id' => 'DESC']);
    }

    /**
     * @param SelectQuery $query
     *
     * @return void
     */
    public function configureQuery(SelectQuery $query)
    {
        $query
            ->cols([
                'doe.id',
                'doe.datum',
                'doe.timestamp',
                'doe.status',
            ])
            ->from('datevconnect_online_export AS doe');
    }

    /**
     * @param ColumnCollection $columns
     *
     * @return void
     */
    public function configureColumns(ColumnCollection $columns)
    {
        $columns->add(Column::hidden('id', 'id'));
        $columns->add(Column::searchable('datum', 'Zeitraum'));
        $columns->add(Column::searchable('timestamp', 'Zeitstempel'));
        $columns->add(Column::searchable('status', 'Status'));
    }

    /**
     * @param FeatureCollection $features
     *
     * @return void
     */
    public function configureFeatures(FeatureCollection $features)
    {
        parent::configureFeatures($features);
        $features->remove(StateSaveFeature::class);
    }
}
