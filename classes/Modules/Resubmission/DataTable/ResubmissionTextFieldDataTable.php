<?php

namespace Xentral\Modules\Resubmission\DataTable;

use Aura\SqlQuery\Exception as AuraSqlQueryException;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Widgets\DataTable\Column\Column;
use Xentral\Widgets\DataTable\Column\ColumnCollection;
use Xentral\Widgets\DataTable\Column\ColumnFormatter;
use Xentral\Widgets\DataTable\Feature\FeatureCollection;
use Xentral\Widgets\DataTable\Feature\TableControlFeature;
use Xentral\Widgets\DataTable\Options\DataTableOptions;
use Xentral\Widgets\DataTable\Type\AbstractDataTableType;

final class ResubmissionTextFieldDataTable extends AbstractDataTableType
{
    /**
     * @param SelectQuery $query
     *
     * @throws AuraSqlQueryException
     *
     * @return void
     */
    public function configureQuery(SelectQuery $query)
    {
        $query
            ->cols([
                'wfk.id',
                'wfk.title',
                'wsa.name' => 'available_from_stage',
                'wsr.name' => 'required_from_stage',
                'wfk.show_in_pipeline',
                'wfk.show_in_tables',
            ])
            ->from('wiedervorlage_freifeld_konfiguration AS wfk')
            ->leftJoin('wiedervorlage_stages AS wsa', 'wsa.id = wfk.available_from_stage_id')
            ->leftJoin('wiedervorlage_stages AS wsr', 'wsr.id = wfk.required_from_stage_id');
    }

    /**
     * @param ColumnCollection $columns
     *
     * @return void
     */
    public function configureColumns(ColumnCollection $columns)
    {
        $menu = Column::fixed('menu', 'Menü', 'center', '1%');
        $menu->setFormatter(static function ($value, $row) {
            $html =
                '<table class="datatable-menu" align="center" border="0" cellpadding="0" cellspacing="0"><tr>' .
                '<td><a href="#" class="resubmissiontextfield-edit-button" data-textfield-config-id="{ID}" ' .
                'title="Freifeld bearbeiten">' .
                '<img src="themes/new/images/edit.svg" alt="Freifeld bearbeiten" border="0" align="center">' .
                '</a></td>' .
                '<td><a href="#" class="resubmissiontextfield-delete-button" data-textfield-config-id="{ID}" ' .
                'title="Freifeld löschen">' .
                '<img src="themes/new/images/delete.svg" alt="Freifeld löschen" border="0" align="center">' .
                '</a></td>' .
                '</tr></table>';

            $html = str_replace('{ID}', $row['id'], $html);

            return $html;
        });

        $availableFromStage = Column::searchable('available_from_stage', 'Verfügbar ab Stage');
        $availableFromStage->setFormatter(ColumnFormatter::ifEmpty('- Immer -'));

        $requiredFromStage = Column::searchable('required_from_stage', 'Pflichtfeld ab Stage');
        $requiredFromStage->setFormatter(ColumnFormatter::ifEmpty('- Nie -'));

        $showInPipeline = Column::sortable('show_in_pipeline', 'Anzeigen in Pipeline');
        $showInPipeline->setFormatter(static function ($value) {
            return (int)$value === 1 ? 'Ja' : 'Nein';
        });

        $showInTables = Column::sortable('show_in_tables', 'Anzeigen in Tabellen');
        $showInTables->setFormatter(static function ($value) {
            return (int)$value === 1 ? 'Ja' : 'Nein';
        });

        $columns->add(Column::searchable('title', 'Bezeichnung'));
        $columns->add($availableFromStage);
        $columns->add($requiredFromStage);
        $columns->add($showInPipeline);
        $columns->add($showInTables);
        $columns->add($menu);
    }

    /**
     * @param DataTableOptions $options
     *
     * @return void
     */
    public function configureOptions(DataTableOptions $options)
    {
        $options->setDefaultSorting(['available_from_stage' => 'ASC', 'required_from_stage' => 'ASC']);
    }

    /**
     * @param FeatureCollection $features
     *
     * @return void
     */
    public function configureFeatures(FeatureCollection $features)
    {
        parent::configureFeatures($features);

        /** @var TableControlFeature $control */
        $control = $features->get(TableControlFeature::class);
        $control->hideButtons();
    }
}
