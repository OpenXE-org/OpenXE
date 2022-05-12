<?php

namespace Xentral\Modules\FiskalyApi\DataTable;

use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Widgets\DataTable\Column\Column;
use Xentral\Widgets\DataTable\Column\ColumnCollection;
use Xentral\Widgets\DataTable\Feature\FeatureCollection;
use Xentral\Widgets\DataTable\Feature\StateSaveFeature;
use Xentral\Widgets\DataTable\Options\DataTableOptions;
use Xentral\Widgets\DataTable\Type\AbstractDataTableType;

class FiskalyTseDataTable extends AbstractDataTableType
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
                'f.id',
                'IF(o.display_name <> "", o.display_name, o.name) AS organization',
                'p.name',
                'CONCAT(f.tss_description, " (", f.tss_uuid, ")", IF(f.is_test_environment = 1," (TEST-Client)","")) AS tss_description',
                'CONCAT(f.client_description, " (", f.client_uuid, ")") AS client_description',
                'CONCAT(\'<a href="index.php?module=fiskaly&action=settings_tse&id=\', f.id, \'"><img src="themes/new/images/edit.svg"></a><img class="button-delete" id="delete-\', f.id, \'" src="themes/new/images/delete.svg">\') as menu'
            ])
            ->from('fiskaly_pos_mapping AS f')
            ->leftJoin('fiskaly_organization AS o', 'f.organization_id = o.fiskaly_organization_id')
            ->leftJoin('projekt AS p', 'f.pos_id = p.id');
    }

    /**
     * @param ColumnCollection $columns
     *
     * @return void
     */
    public function configureColumns(ColumnCollection $columns)
    {
        $columns->add(Column::hidden('id', 'id'));
        $columns->add(Column::searchable('organization', 'Filiale'));
        $columns->add(Column::searchable('name', 'POS Projekt'));
        $columns->add(Column::searchable('tss_description', 'TSS'));
        $columns->add(Column::searchable('client_description', 'Client'));
        $columns->add(Column::fixed('menu', 'Menü'));
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
