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
 * Class FileTable
 *
 * @todo Berechtigungen prüfen
 */
class FileTable
{
  /** @var array $validTypes */
  protected static $validTypes = ['adressen', 'bestellung', 'kasse', 'reisekosten', 'verbindlichkeit'];

  /** @var erpooSystem $app */
  protected $app;

  /** @var string $type */
  protected $type;

  /** @var int $fileId */
  protected $fileId;

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
   * @param string      $documentType
   * @param int         $fileId
   */
  public function __construct($app, $documentType, $fileId)
  {
    $this->app = $app;
    $this->fileId = (int)$fileId;
    $this->type = strtolower($documentType);

    $this->Init();
  }

  /**
   * @return void
   */
  protected function Init()
  {
    switch ($this->type) {
      case 'adressen':
        $table = $this->GetAdressenTable();
        break;

      case 'bestellung':
        $table = $this->GetBestellungenTable();
        break;

      case 'kasse':
        $table = $this->GetKassenbuchTable();
        break;

      case 'reisekosten':
        $table = $this->GetReisekostenTable();
        break;

      case 'verbindlichkeit':
        $table = $this->GetVerbindlichkeitenTable();
        break;

      default:
        throw new RuntimeException(sprintf(
          'Type "%s" is not valid. Valid types are: %s', $this->type, implode(', ', self::$validTypes)
        ));
        break;
    }

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
    $where = '';

    // Suche
    if ($searchQuery !== null){
      $searchParts = [];
      foreach ($this->columns as $column) {
        if (!isset($column['search'])) {
          continue;
        }
        $searchParts[] = sprintf(' %s LIKE \'%%%s%%\' ', $column['search'], $searchQuery);
      }
      $where .= ' AND (' . implode(' OR ', $searchParts) .') ';
    }

    // Filter
    if (!empty($filterParams)) {
      foreach ($filterParams as $filterName => $filterActive) {
        $where .= $this->GenerateFilterSql($filterName, $filterActive);
      }
    }

    // Sortierung
    $orderColumnName = array_column($this->columns, 'data')[$orderCol];
    $orderDirection = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';
    $orderBy = !empty($orderColumnName) ? $orderColumnName . ' ' . $orderDirection . ' ' : '';

    // SQL zusammenbauen
    $sql = $this->sqlTemplate;
    $sql = str_replace(
      ['{{limit}}', '{{offset}}', '{{where}}', '{{orderby}}'],
      [(int)$limit, (int)$offset, $where, $orderBy],
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
   * @return string Filter-SQL
   */
  protected function GenerateFilterSql($filtername, $active = false)
  {
    foreach ($this->GetFilter() as $item) {
      if ($item['name'] === $filtername) {
        return (bool)$active ? $item['sql']['active'] : $item['sql']['inactive'];
      }
    }

    return '';
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
      'class' => 'dt-center',
      'data' => null,
      'sortable' => false,
      'searchable' => false,
      'defaultContent' => sprintf(
        '<a href="#" class="document-assign-action" title="Datei zuweisen">' .
        '<img src="themes/%s/images/forward.svg" align="center" alt="Datei zuweisen">' .
        '</a>',
        $this->app->Conf->WFconf['defaulttheme']),
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
   * @return string
   */
  public function GetTabsHtml()
  {
    return
      '<div id="filetabs">
        <ul>
          <li data-type="verbindlichkeit"><a href="#verbindlichkeit-tab">Verbindlichkeiten</a></li>
          <li data-type="kasse"><a href="#kasse-tab">Kassenbuch</a></li>
          <li data-type="reisekosten"><a href="#reisekosten-tab">Reisekosten</a></li>
          <li data-type="bestellung"><a href="#bestellung-tab">Bestellungen</a></li>
          <li data-type="adressen"><a href="#adressen-tab">Adressen</a></li>
        </ul>
        <div id="adressen-tab"></div>
        <div id="bestellung-tab"></div>
        <div id="kasse-tab"></div>
        <div id="reisekosten-tab"></div>
        <div id="verbindlichkeit-tab"></div>
      </div>';
  }

  /**
   * @return string
   */
  public function GetTabContentHtml()
  {
    // Prüfen ob Modul vorhanden ist
    if (in_array($this->type, ['kasse', 'reisekosten', 'verbindlichkeit'], true)) {
      $moduleName = $this->type;
      if ($this->type === 'adressen') {
          $moduleName = 'adresse';
      }
      if(!$this->app->erp->ModulVorhanden($moduleName)){
        $html = '<div class="module-disabled">';
        $html .= sprintf(
          '<div class="info">Das Modul &quot;%s&quot; ist nicht vorhanden.</div>',
          ucfirst($moduleName)
        );
        $html .= '</div>';

        return $html;
      }
    }

    $html = '<div class="document-table-container">';
    $html .= $this->GetFilterHtml();
    $html .= $this->GetTableHtml();
    $html .= '</div>';

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
          '<label><input type="checkbox" class="document-filter-checkbox" data-filter-name="%s" data-filter-column="%s" %s> %s</label>',
          $item['name'],
          $item['column'],
          ((bool)$item['active'] === true) ? 'checked="checked"' : '',
          $item['label']
        );
      }
      $html .= '</div>';

      if ($this->type === 'verbindlichkeit') {
        $html .= '<div class="col-md-3">';
				$html .= '<input type="button" class="btnGreen create-liability-button" value="Verbindlichkeit anlegen">';
        $html .= '</div>';
      }

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
    $html = '<table class="display" border="0" cellpadding="0" cellspacing="0">';
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
      'label' => 'Nur Adressen ohne Dateien',
      'name' => 'dateianzahl',
      'column' => 8,
      'sql' => [
        'active' => ' AND dateien.anzahl IS NULL ',
        'inactive' => '',
      ],
    ]];

    $columns = [
      [
        'title' => '',
        'data' => 'id',
        'visible' => false,
        'searchable' => false,
      ],
      [
        'title' => 'Name',
        'data' => 'name',
        'search' => 'a.name',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Kunde',
        'data' => 'kundennummer',
        'search' => 'a.kundennummer',
        'class' => 'dt-center',
      ],
      [
        'title' => 'Lieferant',
        'data' => 'lieferantennummer',
        'search' => 'a.lieferantennummer',
        'class' => 'dt-center',
      ],
      [
        'title' => 'Land',
        'data' => 'land',
        'search' => 'a.land',
        'class' => 'dt-left',
      ],
      [
        'title' => 'PLZ',
        'data' => 'plz',
        'search' => 'a.plz',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Ort',
        'data' => 'ort',
        'search' => 'a.ort',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Projekt',
        'data' => 'projekt',
        'search' => 'p.name',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Dateien',
        'data' => 'dateianzahl',
        'class' => 'dt-center',
        'defaultContent' => 0,
      ],
    ];

    $sqlTemplate =
      "SELECT SQL_CALC_FOUND_ROWS a.id,
        CONCAT(a.name, IF(a.ansprechpartner != '', '<br><i style=color:#999>',''), a.ansprechpartner, IF(a.ansprechpartner != '', '</i>', '')) AS name,
        if(a.kundennummer!='',a.kundennummer,'-') AS kundennummer,
        if(a.lieferantennummer!='',a.lieferantennummer,'-') AS lieferantennummer, 
        a.land AS land, 
        a.plz AS plz, 
        a.ort AS ort, 
        p.abkuerzung AS projekt,
        dateien.anzahl AS dateianzahl
      FROM adresse AS a 
      LEFT JOIN projekt AS p ON p.id = a.projekt 
      LEFT JOIN (
        SELECT ds.parameter AS adresse_id, COUNT(ds.datei) AS anzahl 
        FROM datei_stichwoerter AS ds 
        WHERE ds.objekt LIKE 'Adressen'
        GROUP BY ds.parameter, ds.objekt
      ) AS dateien ON dateien.adresse_id = a.id
      WHERE a.geloescht = 0 " . $this->app->erp->ProjektRechte('p.id', true, 'a.vertrieb') . ' 
      {{where}}
      ORDER BY {{orderby}}
      LIMIT {{offset}}, {{limit}}';

    $sqlCount =
      'SELECT COUNT(a.id) AS num FROM adresse a 
      LEFT JOIN projekt p ON p.id = a.projekt 
      WHERE a.geloescht = 0 ' . $this->app->erp->ProjektRechte('p.id', true, 'a.vertrieb');

    return [
      'sql_count' => $sqlCount,
      'sql_template' => $sqlTemplate,
      'columns' => $columns,
      'filter' => $filter,
      'order' => [[1, 'asc']],
    ];
  }

  protected function GetBestellungenTable()
  {
    $filter = [[
      'active' => true,
      'label' => 'Nur Bestellungen ohne Dateien',
      'name' => 'dateianzahl',
      'column' => 9,
      'sql' => [
        'active' => ' AND dateien.anzahl IS NULL ',
        'inactive' => '',
      ],
    ]];

    $columns = [
      [
        'title' => '',
        'data' => 'id',
        'searchable' => false,
        'visible' => false,
      ],
      [
        'title' => 'Bestellung',
        'data' => 'belegnr',
        'search' => 'b.belegnr',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Vom',
        'data' => 'vom',
        'search' => 'DATE_FORMAT(b.datum, "%d.%m.%Y")',
        'class' => 'dt-center',
      ],
      [
        'title' => 'Lf-Nr.',
        'data' => 'lieferantennummer',
        'search' => 'adr.lieferantennummer',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Lieferant',
        'data' => 'lieferant',
        'search' => 'CONCAT(b.name, " ", b.internebezeichnung)',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Land',
        'data' => 'land',
        'search' => 'b.land',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Projekt',
        'data' => 'projekt',
        'search' => 'p.name',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Betrag (brutto)',
        'data' => 'summe',
        'search' => $this->app->erp->FormatPreis('b.gesamtsumme',2),
        'class' => 'dt-right',
      ],
      [
        'title' => 'Status',
        'data' => 'status',
        'search' => 'b.status',
        'class' => 'dt-center',
      ],
      [
        'title' => 'Dateien',
        'data' => 'dateianzahl',
        'class' => 'dt-center',
        'defaultContent' => 0,
      ],
    ];

    $extended_mysql55 = ", 'de_DE'";

    $sqlTemplate =
      "SELECT SQL_CALC_FOUND_ROWS b.id,
        IF(b.status = 'storniert', CONCAT(b.belegnr), b.belegnr) AS belegnr, 
        IF(b.status = 'storniert', CONCAT(DATE_FORMAT(b.datum,'%d.%m.%Y')), DATE_FORMAT(b.datum,'%d.%m.%Y')) AS vom, 
        IF(b.status = 'storniert', CONCAT(adr.lieferantennummer), adr.lieferantennummer) AS lieferantennummer,  
        IF(b.status = 'storniert', CONCAT(" . $this->app->erp->MarkerUseredit("b.name", "b.useredittimestamp") . ", IF(b.internebezeichnung != '', CONCAT('<br><i style=color:#999>', b.internebezeichnung, '</i>'), '')), CONCAT(" . $this->app->erp->MarkerUseredit("b.name", "b.useredittimestamp") . ", IF(b.internebezeichnung != '', CONCAT('<br><i style=color:#999>', b.internebezeichnung, '</i>'), ''))) AS lieferant,  
        IF(b.status = 'storniert', CONCAT(b.land), b.land) AS land, 
        IF(b.status = 'storniert', CONCAT(p.abkuerzung), p.abkuerzung) AS projekt,
        IF(b.status = 'storniert', CONCAT(FORMAT(b.gesamtsumme, 2{$extended_mysql55})), FORMAT(b.gesamtsumme, 2{$extended_mysql55})) AS summe, 
        IF(b.status = 'storniert', CONCAT('<font color=red>', UPPER(b.status), '</font>'), UPPER(b.status)) AS status,
        dateien.anzahl AS dateianzahl 
      FROM bestellung AS b 
      LEFT JOIN projekt AS p ON p.id = b.projekt 
      LEFT JOIN adresse AS adr ON b.adresse = adr.id
      LEFT JOIN (
        SELECT ds.parameter AS bestellung_id, COUNT(ds.datei) AS anzahl 
        FROM datei_stichwoerter AS ds 
        WHERE ds.objekt LIKE 'Bestellung'
        GROUP BY ds.parameter, ds.objekt
      ) AS dateien ON dateien.bestellung_id = b.id
      WHERE b.id != '' AND b.status != 'angelegt' " . $this->app->erp->ProjektRechte() . ' 
      {{where}} 
      ORDER BY {{orderby}}
      LIMIT {{offset}}, {{limit}}';

    $sqlCount =
      'SELECT COUNT(b.id) AS num 
      FROM bestellung AS b 
      LEFT JOIN projekt AS p ON p.id = b.projekt 
      WHERE b.status != \'angelegt\' ' . $this->app->erp->ProjektRechte();

    return [
      'sql_count' => $sqlCount,
      'sql_template' => $sqlTemplate,
      'columns' => $columns,
      'filter' => $filter,
      'order' => [[1, 'desc']],
    ];
  }

  protected function GetKassenbuchTable()
  {
    $filter = [[
      'active' => true,
      'label' => 'Nur Kassenbuch-Einträge ohne Dateien',
      'name' => 'dateianzahl',
      'column' => 8,
      'sql' => [
        'active' => ' AND dateien.anzahl IS NULL ',
        'inactive' => '',
      ],
    ]];

    $columns = [
      [
        'title' => '',
        'data' => 'id',
        'searchable' => false,
        'visible' => false,
      ],
      [
        'title' => 'Nr.',
        'data' => 'nummer',
        'search' => 'k.nummer',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Kasse',
        'data' => 'bezeichnung',
        'search' => 'kon.bezeichnung',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Datum',
        'data' => 'datum',
        'search' => 'DATE_FORMAT(k.datum, "%d.%m.%Y")',
        'class' => 'dt-center',
      ],
      [
        'title' => 'Firma',
        'data' => 'firmenname',
        'search' => 'a.name',
        'class' => 'dt-center',
      ],
      [
        'title' => 'Name/Verwendungszweck',
        'data' => 'verwendungszweck',
        'search' => 'k.grund',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Konto',
        'data' => 'kontoname',
        'search' => 'CONCAT(k.sachkonto, " ", ko.beschriftung)',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Projekt',
        'data' => 'projekt',
        'search' => 'p.abkuerzung',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Dateien',
        'data' => 'dateianzahl',
        'class' => 'dt-center',
        'defaultContent' => 0,
      ],
    ];

    // SQL statement
    $sqlTemplate =
      "SELECT SQL_CALC_FOUND_ROWS k.id, 
        k.nummer,
        kon.bezeichnung,
        IF(k.wert < 0, CONCAT('<font color=red>', DATE_FORMAT(k.datum,'%d.%m.%Y'), '</font>'), DATE_FORMAT(k.datum,'%d.%m.%Y')) AS datum,
        a.name AS firmenname,
        CONCAT(
          k.grund, 
          IF(k.adresse > 0, CONCAT(' (',if(a.kundennummer != '', a.kundennummer, a.mitarbeiternummer), ' ', a.name, ')<br>'), '<br>'), 
          IF(k.storniert_grund != '', CONCAT('<i style=color:#999>', k.storniert_grund, '</i>'), '')
        ) AS verwendungszweck,
        CONCAT(k.sachkonto, ' ', LEFT(ko.beschriftung, 30)) AS kontoname,
        p.abkuerzung AS projekt,
        dateien.anzahl AS dateianzahl
      FROM kasse AS k 
      INNER JOIN konten AS kon ON k.konto = kon.id
      LEFT JOIN adresse AS a ON a.id = k.adresse 
      LEFT JOIN kontorahmen AS ko ON ko.sachkonto = k.sachkonto 
      LEFT JOIN projekt AS p ON p.id = k.projekt 
      LEFT JOIN (
        SELECT ds.parameter AS kasse_id, COUNT(ds.datei) AS anzahl 
        FROM datei_stichwoerter AS ds 
        WHERE ds.objekt LIKE 'Kasse'
        GROUP BY ds.parameter, ds.objekt
      ) AS dateien ON dateien.kasse_id = k.id
      WHERE 1 " . $this->app->erp->ProjektRechte() . ' 
      {{where}} 
      ORDER BY {{orderby}}
      LIMIT {{offset}}, {{limit}}';

    $sqlCount =
      'SELECT COUNT(k.id) AS num 
      FROM kasse AS k 
      LEFT JOIN projekt AS p ON p.id = k.projekt
      WHERE 1 ' . $this->app->erp->ProjektRechte();

    return [
      'sql_count' => $sqlCount,
      'sql_template' => $sqlTemplate,
      'columns' => $columns,
      'filter' => $filter,
      'order' => [[1, 'desc']],
    ];
  }

  protected function GetReisekostenTable()
  {
    $filter = [[
      'active' => true,
      'label' => 'Nur Reisekosten ohne Dateien',
      'name' => 'dateianzahl',
      'column' => 9,
      'sql' => [
        'active' => ' AND dateien.anzahl IS NULL ',
        'inactive' => '',
      ],
    ]];

    $columns = [
      [
        'title' => '',
        'data' => 'id',
        'searchable' => false,
        'visible' => false,
      ],
      [
        'title' => 'Mitarbeiter',
        'data' => 'mitarbeiter',
        'search' => 'ma.name',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Reisekosten',
        'data' => 'belegnr',
        'search' => 'l.belegnr',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Vom',
        'data' => 'vom',
        'search' => 'DATE_FORMAT(l.datum, "%d.%m.%Y")',
        'class' => 'dt-center',
      ],
      [
        'title' => 'Kd-Nr.',
        'data' => 'kundennummer',
        'search' => 'adr.kundennummer',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Kunde',
        'data' => 'kundenname',
        'search' => 'l.name',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Anlass',
        'data' => 'anlass',
        'search' => 'l.anlass',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Projekt',
        'data' => 'projekt',
        'search' => 'p.name',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Status',
        'data' => 'status',
        'search' => 'l.status',
        'class' => 'dt-center',
      ],
      [
        'title' => 'Dateien',
        'data' => 'dateianzahl',
        'class' => 'dt-center',
        'defaultContent' => 0,
      ],
    ];

    $sqlTemplate =
      "SELECT SQL_CALC_FOUND_ROWS l.id,
        ma.name AS mitarbeiter, 
        l.belegnr, 
        DATE_FORMAT(l.datum, '%d.%m.%Y') AS vom, 
        adr.kundennummer AS kundennummer, 
        l.name AS kundenname, 
        l.anlass AS anlass, 
        p.abkuerzung AS projekt,
        UPPER(l.status) AS status,
        dateien.anzahl AS dateianzahl 
      FROM reisekosten AS l 
      LEFT JOIN projekt AS p ON p.id = l.projekt 
      LEFT JOIN adresse AS adr ON l.adresse = adr.id 
      LEFT JOIN adresse AS ma ON ma.id = l.mitarbeiter 
      LEFT JOIN (
        SELECT ds.parameter AS reisekosten_id, COUNT(ds.datei) AS anzahl 
        FROM datei_stichwoerter AS ds 
        WHERE ds.objekt LIKE 'Reisekosten'
        GROUP BY ds.parameter, ds.objekt
      ) AS dateien ON dateien.reisekosten_id = l.id
      WHERE l.id != '' AND l.status != 'angelegt' " . $this->app->erp->ProjektRechte() . ' 
      {{where}} 
      ORDER BY {{orderby}}
      LIMIT {{offset}}, {{limit}}';

    $sqlCount =
      "SELECT COUNT(l.id) AS num 
      FROM reisekosten AS l 
      LEFT JOIN projekt AS p ON p.id = l.projekt 
      WHERE l.id != '' AND l.status != 'angelegt' " . $this->app->erp->ProjektRechte();

    return [
      'sql_count' => $sqlCount,
      'sql_template' => $sqlTemplate,
      'columns' => $columns,
      'filter' => $filter,
      'order' => [[2, 'desc']],
    ];
  }

  /**
   * @return array
   */
  protected function GetVerbindlichkeitenTable()
  {
    $filter = [[
      'active' => true,
      'label' => 'Nur Verbindlichkeiten ohne Dateien',
      'name' => 'dateianzahl',
      'column' => 7,
      'sql' => [
        'active' => ' AND dateien.anzahl IS NULL ',
        'inactive' => '',
      ],
    ]];

    $columns = [
      [
        'title' => '',
        'data' => 'id',
        'searchable' => false,
        'visible' => false,
      ],
      [
        'title' => 'Nr.',
        'data' => 'verbindlichkeitnr',
        'search' => 'v.belegnr',
        'class' => 'dt-left',
      ],
      [
        'title' => 'Lf-Nr.',
        'data' => 'lieferantennr',
        'search' => 'a.lieferantennummer',
        'class' => 'dt-center',
      ],
      [
        'title' => 'Lieferant',
        'data' => 'lieferant',
        'search' => 'a.name',
        'class' => 'dt-left',
      ],
      [
        'title' => 'RE-Datum',
        'data' => 'rechnungsdatum',
        'search' => 'DATE_FORMAT(v.rechnungsdatum, "%d.%m.%Y")',
        'class' => 'dt-center',
      ],
      [
        'title' => 'RE-Nr.',
        'data' => 'rechnungsnr',
        'search' => 'v.rechnung',
        'class' => 'dt-center',
      ],
      [
        'title' => 'Betrag',
        'data' => 'betrag',
        'search' => $this->app->erp->FormatPreis('v.betrag',2),
        'class' => 'dt-right',
      ],
      [
        'title' => 'Dateien',
        'data' => 'dateianzahl',
        'class' => 'dt-center',
        'defaultContent' => 0,
      ],
    ];

    $sqlTemplate =
      "SELECT SQL_CALC_FOUND_ROWS v.id, 
        if(v.status <> 'storniert', v.belegnr, concat('<s>', v.belegnr, '</s>')) AS verbindlichkeitnr, 
        if(v.status <> 'storniert', a.lieferantennummer,concat('<s>', a.lieferantennummer, '</s>')) AS lieferantennr,
        if(v.status <> 'storniert', a.name, concat('<s>', a.name, '</s>')) AS lieferant,
        DATE_FORMAT(v.rechnungsdatum, '%d.%m.%Y') AS rechnungsdatum, 
        IF(v.betrag < 0, CONCAT('<font color=red>', v.verwendungszweck, ' ', IF(v.rechnung != '', 'RE ' ,''), v.rechnung, '</font>'), v.rechnung) AS rechnungsnr,
        (".$this->app->erp->FormatPreis('v.betrag', 2).") AS betrag,
        dateien.anzahl AS dateianzahl 
      FROM verbindlichkeit v 
      LEFT JOIN adresse AS a ON v.adresse = a.id 
      LEFT JOIN zahlungsweisen AS z ON v.zahlungsweise = z.type 
      LEFT JOIN projekt AS p ON p.id = v.projekt
      LEFT JOIN (
        SELECT ds.parameter AS verbindlichkeit_id, COUNT(ds.datei) AS anzahl 
        FROM datei_stichwoerter AS ds 
        WHERE ds.objekt LIKE 'Verbindlichkeit'
        GROUP BY ds.parameter, ds.objekt
      ) AS dateien ON dateien.verbindlichkeit_id = v.id
      WHERE v.id > 0 " . $this->app->erp->ProjektRechte() . ' 
      {{where}} 
      ORDER BY {{orderby}}
      LIMIT {{offset}}, {{limit}}';

    $sqlCount =
      'SELECT COUNT(v.id) AS num 
      FROM verbindlichkeit AS v 
      LEFT JOIN projekt AS p ON p.id = v.projekt
      WHERE v.id > 0 ' . $this->app->erp->ProjektRechte();

    return [
      'sql_count' => $sqlCount,
      'sql_template' => $sqlTemplate,
      'columns' => $columns,
      'filter' => $filter,
      'order' => [[0, 'desc']],
    ];
  }
}
