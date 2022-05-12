<?php
/*
**** COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
* 
* Xentral (c) Xentral ERP Sorftware GmbH, Fuggerstrasse 11, D-86150 Augsburg, * Germany 2019
*
* This file is licensed under the Embedded Projects General Public License *Version 3.1. 
*
* You should have received a copy of this license from your vendor and/or *along with this file; If not, please visit www.wawision.de/Lizenzhinweis 
* to obtain the text of the corresponding license version.  
*
**** END OF COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
*/
?>
<?php

/**
 * Class DownloadSpoolerTable
 */
class DownloadSpoolerTable
{
  /** @var erpooSystem $app */
  protected $app;

  /** @var int $printerId */
  protected $printerId;

  /** @var array $columns */
  protected $columns = [];

  /** @var array $filter */
  protected $filter = [];

  /** @var array $order Initiale Sortierung */
  protected $order = [[0, 'asc']];

  /** @var string $sqlTemplate */
  protected $sqlTemplate = '';

  /** @var string $sqlCount */
  protected $sqlCount = '';

  /**
   * @param erpooSystem $app
   * @param int         $printerId
   */
  public function __construct($app, $printerId)
  {
    $this->app = $app;
    $this->printerId = (int)$printerId;

    $this->Init();
  }

  /**
   * @return void
   */
  protected function Init()
  {
    $table = $this->GetAdressenTable();

    $this->sqlTemplate = $table['sql_template'];
    $this->sqlCount = $table['sql_count'];
    $this->columns = $table['columns'];
    $this->filter = $table['filter'];
    $this->order = $table['order'];
  }

  /**
   * @param array $filterParams
   * @param string|null $searchQuery Suchbegriff
   * @param int $orderCol Spalten-Index
   * @param string $orderDir [asc|desc]
   * @param int $offset
   * @param int $limit
   * @param int $draw
   *
   * @return array
   */
  public function GetData(
    $filterParams = [],
    $searchQuery = null,
    $orderCol = 0,
    $orderDir = 'asc',
    $offset = 0,
    $limit = 10,
    $draw = 1
  )
  {
    $where = [];

    // Suche
    if ($searchQuery !== null){
      $searchParts = [];
      foreach ($this->columns as $column) {
        if (!isset($column['search'])) {
          continue;
        }
        $searchParts[] = sprintf(' %s LIKE \'%%%s%%\' ', $column['search'], $searchQuery);
      }
      $where[] = '(' . implode(' OR ', $searchParts) .')';
    }

    // Filter
    if (!empty($filterParams)) {
      foreach ($filterParams as $filterName => $filterActive) {
        $where[] = $this->GenerateFilterSql($filterName, $filterActive);
      }
    }
    $whereString = implode(' AND ', $where);
    if (empty($whereString)) {
      $whereString = ' 1 ';
    }

    // Sortierung
    $orderColumnName = !empty(array_column($this->columns, 'search')[$orderCol])?
      array_column($this->columns, 'search')[$orderCol]:
      array_column($this->columns, 'data')[$orderCol];
    $orderDirection = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';
    $orderBy = !empty($orderColumnName) ? $orderColumnName . ' ' . $orderDirection . ' ' : '';

    // SQL zusammenbauen
    $sql = $this->sqlTemplate;
    $sql = str_replace(
      ['{{limit}}', '{{offset}}', '{{where}}', '{{orderby}}'],
      [(int)$limit, (int)$offset, $whereString, $orderBy],
      $sql
    );

    // Ergebnisse abrufen
    $result = $this->app->DB->SelectArr($sql);
    $countFiltered = $this->app->DB->Select('SELECT FOUND_ROWS()');
    $countTotal = $this->app->DB->Select($this->sqlCount);

    return [
      'draw' => (int)$draw,
      'recordsTotal' => (int)$countTotal,
      'recordsFiltered' => (int)$countFiltered,
      'data' => (array)$result,
    ];
  }

  /**
   * @param string $filtername
   * @param bool   $active
   *
   * @return string|null Filter-SQL
   */
  protected function GenerateFilterSql($filtername, $active = false)
  {
    foreach ($this->GetFilter() as $item) {
      if ($item['name'] === $filtername) {
        return (bool)$active ? $item['sql']['active'] : $item['sql']['inactive'];
      }
    }

    return null;
  }

  /**
   * Getter für DataTable-Einstellungen
   *
   * @param string $ajaxUrl
   *
   * @return array
   */
  public function GetSettings($ajaxUrl)
  {
    return [
      'ajax' => $ajaxUrl,
      'columns' => $this->GetColumns(),
      'order' => $this->order,
      'processing' => true,
      'serverSide' => true,
      'responsive' => true,
      'autoWidth' => true,
      'paging' => true,
      'language' => [
        'decimal' => ',',
        'thousands' => '.',
        'paginate' => [
          'first' => 'Erste',
          'last' => 'Letzte',
          'next' => '>>',
          'previous' => '<<',
        ],
        'emptyTable' => 'Keine Daten vorhanden',
        'info' => 'Zeige _START_ bis _END_ von _TOTAL_ Einträgen',
        'infoEmpty' => 'Zeige 0 bis 0 von 0 Einträge',
        'infoFiltered' => '(gefiltert von _MAX_ Einträgen)',
        'infoPostFix' => '',
        'lengthMenu' => '_MENU_ Eintr&auml;ge pro Seite',
        'loadingRecords' => 'Loading...',
        'processing' => '',
        'search' => 'Suche:',
        'searchPlaceholder' => '',
        'zeroRecords' => 'Keine Einträge gefunden',
        'aria' => [
          'sortAscending' => ': activate to sort column ascending',
          'sortDescending' => ': activate to sort column descending',
        ],
      ],
    ];
  }

  /**
   * Konfiguration für DataTable-Spalten
   *
   * "Zuweisen"-Button als zusätzliche Spalte einfügen
   *
   * @return array
   */
  protected function GetColumns()
  {
    // Prüfen ob Spalte mit ID existiert; wird für Zuweisung benötigt
    $hasIdColumn = false;
    foreach ($this->columns as $column) {
      if ($column['data'] === 'id') {
        $hasIdColumn = true;
      }
    }
    if ($hasIdColumn === false) {
      throw new RuntimeException('ID-Spalte fehlt. Eine Spalte muss mit "data => id" ausgewiesen sein.');
    }

    // "Zuweisen"-Button als zusätzliche Spalte einfügen
    $columns = (array)$this->columns;
    $columns[] = [
      'title' => 'Menü',
      'class' => 'dt-right',
      'width' => '1%',
      'data' => null,
      'sortable' => false,
      'searchable' => false,
      'defaultContent' => '',
    ];

    return $columns;
  }

  /**
   * @return array
   */
  protected function GetFilter()
  {
    if (!is_array($this->filter)) {
      return [];
    }

    return $this->filter;
  }

  /**
   * @return string HTML
   */
  public function GetContentHtml()
  {
    $html = <<<'HTML'
<div class="row">
  <div class="row-height">
    <div class="col-md-12 col-md-height">
      <div class="inside-full-height">
        <div>
          <div class="info">Hinweis: Die Druckeraufträge werden automatisch nach 10 Tagen gelöscht.</div>
HTML;
    $html .= $this->GetFilterHtml();
    $html .= '<form action="./index.php?module=welcome&action=spooler&id=' . $this->printerId . '" method="post">';
    $html .= $this->GetTableHtml();
    $html .= <<<'HTML'
            <fieldset>
              <legend>Stapelverarbeitung</legend>
              <label><input type="checkbox" id="markall_trigger">&nbsp;alle markieren&nbsp;</label>
              <input type="submit" class="btnBlue" value="ZIP erstellen" name="makezip">
              <input type="submit" class="btnBlue" value="Sammel-PDF &ouml;ffnen" name="makepdf">
              <input type="hidden" name="markall_selection" id="markall_selection">
            </fieldset>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
HTML;

    return $html;
  }

  /**
   * @return string
   */
  protected function GetFilterHtml()
  {
    $html = '';
    $filter = $this->GetFilter();

    if (!empty($filter)){
      $html .= '<fieldset>';
      $html .= '<legend>Filter</legend>';
      $html .= '<div class="row">';
      $html .= '<div class="col-md-9">';

      foreach ($filter as $item) {
        $html .= sprintf(
          '<label><input type="checkbox" class="spooler-filter-checkbox" data-filter-name="%s" data-filter-column="%s" %s> %s</label>',
          $item['name'],
          $item['column'],
          ((bool)$item['active'] === true) ? 'checked="checked"' : '',
          $item['label']
        );
      }
      $html .= '</div>';
      $html .= '</fieldset>';
    }

    return $html;
  }

  /**
   * @return string
   */
  protected function GetTableHtml()
  {
    $columns = $this->GetColumns();

    // Tabellenkopf
    $html = '<table id="downloadspooler-table" class="display" border="0" cellpadding="0" cellspacing="0">';
    $html .= '<thead><tr>';
    foreach ($columns as $column) {
      $html .= '<th>' . $column['title'] . '</th>';
    }
    $html .= '</tr></thead>';

    // Tabellenfuß
    $html .= '<tfoot><tr>';
    foreach ($columns as $column) {
      $html .= '<th>' . $column['title'] . '</th>';
    }
    $html .= '</tr></tfoot>';
    $html .= '</table>';

    return $html;
  }

  /**
   * @return array
   */
  protected function GetAdressenTable()
  {
    $filter = [[
      'active' => true,
      'label' => 'Nur ungedruckte Dateien',
      'name' => 'ungedruckt',
      'column' => 5,
      'sql' => [
        'active' => 's.gedruckt = 0',
        'inactive' => '(s.gedruckt = 1 OR s.gedruckt = 0)',
      ],
    ]];

    $columns = [
      [
        'title' => '',
        'data' => 'id',
        'visible' => true,
        'searchable' => false,
        'orderable' => false,
        'search' => 's.id',
      ],
      [
        'title' => 'Zeit',
        'data' => 'zeit',
        'search' => 's.zeitstempel',
        'class' => 'dt-center',
      ],
      [
        'title' => 'Dateiname',
        'data' => 'dateiname',
        'search' => 's.filename',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Drucker',
        'data' => 'drucker',
        'search' => 'd.name',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Bearbeiter',
        'data' => 'bearbeiter',
        'search' => 'a.name',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Gedruckt',
        'data' => 'gedruckt',
        'search' => 's.gedruckt',
        'class' => 'dt-center',
      ],
    ];

    $sqlTemplate =
      "SELECT 
        SQL_CALC_FOUND_ROWS s.id, 
        DATE_FORMAT(s.zeitstempel,'%d.%m.%Y %H:%i:%s') AS zeit, 
        d.name AS drucker, 
        IF(s.filename != '', s.filename, 'Kein Dateiname vorhanden') AS dateiname, 
        a.name AS bearbeiter,
        IF(s.gedruckt = 1, 'ja', 'nein') AS gedruckt
      FROM drucker_spooler AS s 
      LEFT JOIN drucker AS d ON s.drucker = d.id
      LEFT JOIN `user` AS u ON u.id = s.user 
      LEFT JOIN adresse AS a ON a.id = u.adresse 
      WHERE d.id = {$this->printerId} 
      AND {{where}}
      ORDER BY {{orderby}}
      LIMIT {{offset}}, {{limit}}";

    $sqlCount = 'SELECT COUNT(s.id) AS num FROM drucker_spooler AS s WHERE 1';

    return [
      'sql_template' => $sqlTemplate,
      'sql_count' => $sqlCount,
      'columns' => $columns,
      'filter' => $filter,
      'order' => [[1, 'desc']],
    ];
  }
}
