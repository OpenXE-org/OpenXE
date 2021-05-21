<?php

namespace Xentral\Modules\Resubmission\DataTable;

use Aura\SqlQuery\Exception as AuraSqlQueryException;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Widgets\DataTable\Column\Column;
use Xentral\Widgets\DataTable\Column\ColumnCollection;
use Xentral\Widgets\DataTable\Feature\FeatureCollection;
use Xentral\Widgets\DataTable\Feature\TableControlFeature;
use Xentral\Widgets\DataTable\Filter\CustomFilter;
use Xentral\Widgets\DataTable\Filter\FilterCollection;
use Xentral\Widgets\DataTable\Options\DataTableOptions;
use Xentral\Widgets\DataTable\Request\DataTableRequest;
use Xentral\Widgets\DataTable\Type\AbstractDataTableType;

final class ResubmissionTasksDataTable extends AbstractDataTableType
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
                'a.id',
                'a.aufgabe'             => 'title',
                'adr.name'              => 'employee_name',
                'adr.mitarbeiternummer' => 'employee_number',
                'a.abgabe_bis'          => 'completion_date',
                'a.startdatum'          => 'start_date',
                'a.startzeit'           => 'start_time',
                'a.prio'                => 'priority',
                'a.status'              => 'state',
            ])
            ->from('aufgabe AS a')
            ->innerJoin('wiedervorlage_aufgabe AS wa', 'wa.task_id = a.id')
            ->leftJoin('adresse AS adr', 'a.adresse = adr.id');
    }

    /**
     * @param ColumnCollection $columns
     *
     * @return void
     */
    public function configureColumns(ColumnCollection $columns)
    {
        $priority = Column::searchable('priority', 'Priorität', Column::ALIGN_CENTER);
        $priority->setFormatter(static function ($value) {
            $prio = (int)$value;
            if ($prio === 1) {
                return 'hoch';
            }
            if ($prio === -1) {
                return 'niedrig';
            }

            return 'mittel';
        });

        $employee = Column::searchable('employee', 'Mitarbeiter');
        $employee->setFormatter(function ($value, $row) {
            if (!empty($row['employee_number'])) {
                return sprintf('%s %s', $row['employee_number'], $row['employee_name']);
            }

            return (string)$row['employee_name'];
        });

        $menu = Column::fixed('menu', 'Menü', 'center', '1%');
        $menu->setFormatter(function ($value, $row) {
            $stateText = $row['state'] === 'abgeschlossen' ? 'completed' : 'open';
            $stateIcon = $row['state'] === 'abgeschlossen' ? 'check_circle_filled.svg' : 'check_circle_outlined.svg';
            $html =
                '<table class="datatable-menu" align="center" border="0" cellpadding="0" cellspacing="0"><tr>' .
                '<td><a href="#" class="resubmissiontask-state-button" data-task-id="{ID}" ' .
                'data-task-state="{STATE_TEXT}" title="Status ändern">' .
                '<img src="themes/new/images/{STATE_ICON}" alt="Status ändern" border="0" align="center">' .
                '</a></td>' .
                '<td><a href="#" class="resubmissiontask-edit-button" data-task-id="{ID}" title="Aufgabe bearbeiten">' .
                '<img src="themes/new/images/edit.svg" alt="Aufgabe bearbeiten" border="0" align="center">' .
                '</a></td>' .
                '<td><a href="#" class="resubmissiontask-delete-button" data-task-id="{ID}"  title="Aufgabe löschen">' .
                '<img src="themes/new/images/delete.svg" alt="Aufgabe löschen" border="0" align="center">' .
                '</a></td>' .
                '</tr></table>';

            $html = str_replace('{ID}', $row['id'], $html);
            $html = str_replace('{STATE_TEXT}', $stateText, $html);
            $html = str_replace('{STATE_ICON}', $stateIcon, $html);

            return $html;
        });

        $columns->add(Column::searchable('title', 'Aufgabe'));
        $columns->add($employee);
        $columns->add(Column::searchable('completion_date', 'Abgabe bis'));
        $columns->add($priority);
        $columns->add($menu);
    }

    /**
     * @param DataTableOptions $options
     *
     * @return void
     */
    public function configureOptions(DataTableOptions $options)
    {
        $options->setDefaultSorting(['completion_date' => 'ASC', 'title' => 'ASC']);
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
        $control->disablePaging();
        $control->disableSearching();
        $control->hideLengthChange();
        $control->hideButtons();
        $control->hideInfo();
    }

    /**
     * @param FilterCollection $filters
     *
     * @return void
     */
    public function configureFilters(FilterCollection $filters)
    {
        $closure = static function (SelectQuery $query, DataTableRequest $request) {
            $resubmissionId = (int)$request->getOriginalRequest()->getParam('id');
            if ($resubmissionId > 0) {
                $query->where('wa.resubmission_id = ?', $resubmissionId);
            }
        };
        $filters->add(new CustomFilter($closure));
    }
}
