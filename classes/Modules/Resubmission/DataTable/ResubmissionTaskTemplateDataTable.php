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

final class ResubmissionTaskTemplateDataTable extends AbstractDataTableType
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
        'wav.id',
        'wav.title',
        'a.name',
        'wav.submission_date_days',
        'wsr.name' => 'required_from_stage',
        'wsa.name' => 'add_task_at_stage',
        'wav.state'
      ])
      ->from('wiedervorlage_aufgabe_vorlage AS wav')
      ->leftJoin('wiedervorlage_stages AS wsr', 'wsr.id = wav.required_from_stage_id')
      ->leftJoin('wiedervorlage_stages AS wsa', 'wsa.id = wav.add_task_at_stage_id')
      ->leftJoin('adresse AS a', 'a.id = wav.employee_address_id');

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
        '<td><a href="#" class="resubmissiontasktemplate-edit-button" data-tasktemplate-config-id="{ID}" ' .
        'title="Vorlage bearbeiten">' .
        '<img src="themes/new/images/edit.svg" alt="Vorlage bearbeiten" border="0" align="center">' .
        '</a></td>' .
        '<td><a href="#" class="resubmissiontasktemplate-delete-button" data-tasktemplate-config-id="{ID}" ' .
        'title="Vorlage löschen">' .
        '<img src="themes/new/images/delete.svg" alt="Vorlage löschen" border="0" align="center">' .
        '</a></td>' .
        '</tr></table>';

      $html = str_replace('{ID}', $row['id'], $html);

      return $html;
    });

    $requiredFromStage = Column::searchable('required_from_stage', 'Pflichtfeld ab');
    $requiredFromStage->setFormatter(ColumnFormatter::ifEmpty('- Nie -'));

    $columns->add(Column::searchable('title', 'Bezeichnung'));
    $columns->add(Column::searchable('name', 'Bearbeiter'));
    $columns->add(Column::searchable('submission_date_days', 'Intervall'));
    $columns->add($requiredFromStage);
    $columns->add(Column::searchable('add_task_at_stage', 'Hinzufügen ab'));

    $state = Column::searchable('state', 'Status');
    $state->setFormatter(function ($value, $row) {
      if ($row['state'] == 'open') {
        return 'Offen';
      }elseif ($row['state'] == 'processing') {
        return 'In Bearbeitung';
      }elseif ($row['state'] == 'completed') {
        return 'Abgeschlossen';
      }

      return (string)$row['state'];
    });
    $columns->add($state);
    $columns->add($menu);
  }

  /**
   * @param DataTableOptions $options
   *
   * @return void
   */
  public function configureOptions(DataTableOptions $options)
  {
    $options->setDefaultSorting(['required_from_stage' => 'ASC', 'add_task_at_stage' => 'ASC']);
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
