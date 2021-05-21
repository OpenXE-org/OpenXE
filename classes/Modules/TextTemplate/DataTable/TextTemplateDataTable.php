<?php

namespace Xentral\Modules\TextTemplate\DataTable;

use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Widgets\DataTable\Column\Column;
use Xentral\Widgets\DataTable\Column\ColumnCollection;
use Xentral\Widgets\DataTable\Column\ColumnFormatter;
use Xentral\Widgets\DataTable\Feature\ColumnFilterFeature;
use Xentral\Widgets\DataTable\Feature\FeatureCollection;
use Xentral\Widgets\DataTable\Feature\TableStylingFeature;
use Xentral\Widgets\DataTable\Type\AbstractDataTableType;

final class TextTemplateDataTable extends AbstractDataTableType
{
    /**
     * @param ColumnCollection $columns
     *
     * @return void
     */
    public function configureColumns(ColumnCollection $columns)
    {
        $menu = Column::fixed('menu', 'Menü', 'center', '5%');
        $menu->setFormatter(ColumnFormatter::template(
            '<table class="datatable-menu" border="0" cellspacing="0" cellpadding="0"><tbody><tr>'.
            '<td><a href="#" class="text-template-edit" data-edit-id="{ID}">' .
            '<img src="themes/new/images/edit.svg" border="0" alt="Bearbeiten"></a></td>'.
            '<td><a href="#" class="text-template-delete" data-delete-id="{ID}">' .
            '<img src="themes/new/images/delete.svg" border="0" alt="Löschen"></a></td>' .
            '<td><a href="#" class="text-template-apply" data-apply-id="{ID}">' .
            '<img src="themes/new/images/forward.svg" border="0" alt="Einfügen"></a></td>' .
            '</tr></tbody></table>'
        ));

        $columns->add(Column::searchable('name', 'Name', 'left', '25%'));
        $columns->add(Column::searchable('text', 'Text', 'left', '40%'));
        $columns->add(Column::searchable('stichwoerter', 'Stichwörter', 'left', '15%'));
        $columns->add(Column::searchable('projekt', 'Projekt', 'center', '15%'));
        $columns->add($menu);
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
                't.id',
                't.name',
                't.text',
                't.stichwoerter',
                't.projekt',
            ])
            ->from('textvorlagen AS t');
    }

    /**
     * @param FeatureCollection $features
     *
     * @return void
     */
    public function configureFeatures(FeatureCollection $features)
    {
        parent::configureFeatures($features);

        $features->set(new ColumnFilterFeature());
        $features->set(new TableStylingFeature(true));
    }
}
