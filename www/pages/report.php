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

use Xentral\Components\Database\Database;
use Xentral\Components\Http\File\FileUpload;
use Xentral\Components\Http\FileResponse;
use Xentral\Components\Http\JsonResponse;
use Xentral\Components\Http\RedirectResponse;
use Xentral\Components\Http\Request;
use Xentral\Components\Http\Response;
use Xentral\Components\Template\Template;
use Xentral\Components\Util\StringUtil;
use Xentral\Modules\Api\Exception\BadRequestException;
use Xentral\Modules\Report\Data\ReportColumn;
use Xentral\Modules\Report\Data\ReportColumnCollection;
use Xentral\Modules\Report\Data\ReportData;
use Xentral\Modules\Report\Data\ReportParameter;
use Xentral\Modules\Report\Data\ReportParameterOptionValue;
use Xentral\Modules\Report\Exception\ColumnDefinitionException;
use Xentral\Modules\Report\Exception\ColumnFormatException;
use Xentral\Modules\Report\Exception\DatabaseTransactionException;
use Xentral\Modules\Report\Exception\InvalidArgumentException;
use Xentral\Modules\Report\Exception\JsonParseException;
use Xentral\Modules\Report\Exception\ReportNoDataException;
use Xentral\Modules\Report\Exception\ReportReadonlyException;
use Xentral\Modules\Report\Exception\ReportSqlQueryException;
use Xentral\Modules\Report\Exception\ReportUserAccessException;
use Xentral\Modules\Report\ReportCsvExportService;
use Xentral\Modules\Report\ReportGateway;
use Xentral\Modules\Report\ReportJsonExportService;
use Xentral\Modules\Report\ReportJsonImportService;
use Xentral\Modules\Report\ReportPdfExportService;
use Xentral\Modules\Report\ReportResolveParameterService;
use Xentral\Modules\Report\ReportService;
use Xentral\Modules\Report\Service\ReportColumnFormatter;

class Report
{
    /** @var string MODULE_NAME */
    const MODULE_NAME = 'Report';

    /** @var array $javascript */
    public $javascript = [
      './classes/Modules/Report/www/js/report_list.js',
      './classes/Modules/Report/www/js/report_edit.js',
      './classes/Modules/Report/www/js/report_share.js',
      './classes/Modules/Report/www/js/report_transfer.js',
      './classes/Modules/Report/www/js/report_parameter_input_dialog.js',
      './classes/Modules/Report/www/js/report_menu_popup.js',
    ];

    /** @var erpooSystem $app */
    private $app;

    /** @var ReportService */
    private $service;

    /** @var ReportGateway */
    private $gateway;

    /** @var TemplateParser $template */
    private $template; //legacy template

    /** @var Request $request */
    private $request;

    /** @var array $parameterNameBlacklist */
    private $parameterNameBlacklist = [
        'MODULE', 'ACTION', 'CMD', 'ID', 'FORMAT', 'USER_ID', 'USER_PROJECTS', 'USER_ADMIN', 'REPORT_PROJECT'
    ];

    /**
     * @param erpooSystem $app
     * @param bool        $intern
     */
    public function __construct($app, $intern = false)
    {
        $this->app = $app;
        if ($intern) {
            return;
        }

        $this->gateway = $this->app->Container->get('ReportGateway');
        $this->service = $this->app->Container->get('ReportService');
        $this->template = $this->app->Tpl;
        $this->app->ActionHandlerInit($this);
        $this->request = $this->app->Container->get('Request');

        // ab hier alle Action Handler definieren die das Modul hat
        $this->app->ActionHandler('list', 'HandleActionList');
        $this->app->ActionHandler('view', 'ReportTable');
        $this->app->ActionHandler('edit', 'HandleActionEdit');
        $this->app->ActionHandler('create', 'HandleActionCreate');
        $this->app->ActionHandler('delete', 'HandleActionDelete');
        $this->app->ActionHandler('download', 'ReportDownload');
        $this->app->ActionHandler('transfer', 'ReportTransfer');
        $this->app->ActionHandler('share', 'HandleActionShare');
        $this->app->ActionHandler('export', 'ReportExport');

        $this->app->ActionHandlerListen($app);
        $this->app->erp->Headlines('Berichte');
    }

    /**
     * @param Application $app
     * @param string      $name
     * @param array       $erlaubtevars
     *
     * @return array
     */
    public function TableSearch($app, $name, $erlaubtevars)
    {
        // in dieses switch alle lokalen Tabellen (diese Live Tabellen mit Suche etc.) für dieses Modul
        switch ($name) {
            case 'report_list':
                $allowed['report'] = ['list'];

                $heading = [
                    'Name',
                    'Kategorie',
                    'Inhalt',
                    'Projekt',
                    'Öffentlich',
                    'Men&uuml;',
                ];
                $aligncenter = [5];
                $width = ['20%', '20%', '35%', '15%', '10%', '1%', '1'];
                $findcols = [
                    'r.name',
                    'r.category',
                    'r.description',
                    'p.abkuerzung',
                    "'ja' as public",
                    'cd.id',
                ];
                $searchsql = [
                    'r.name',
                    'r.category',
                    'r.description',
                    'p.abkuerzung',
                    "'ja' as public",
                ];
                $defaultorder = 1;
                $defaultorderdesc = 0;
                $orderby = '';

                $uiTheme = $app->Conf->WFconf['defaulttheme'];

                $menu = '<table cellpadding=0 cellspacing=0>';
                $menu .= '<tr>';
                $menu .= '<td nowrap>';
                $menu .= '<a href="?module=report&action=view&id=%value%" data-report-id="%value%">';
                $menu .= "<img src=\"themes/$uiTheme/images/forward.png\" alt=\"ansicht\">";
                $menu .= '</a>&nbsp;';

                $menu .= '<a href="#" class="download-csv-button" data-format="pdf" data-report-id="%value%">';
                $menu .= "<img src=\"themes/$uiTheme/images/pdf.png\" alt=\"PDF download\">";
                $menu .= '</a>&nbsp;';

                $menu .= '<a href="#" class="download-csv-button" data-format="csv" data-report-id="%value%">';
                $menu .= "<img src=\"themes/$uiTheme/images/pdf.png\" alt=\"CSV download\">";
                $menu .= '</a>&nbsp;';

                $menu .= '<a href="?module=report&action=download&format=json&id=%value%" data-report-id="%value%">';
                $menu .= "<img src=\"themes/$uiTheme/images/download.png\" alt=\"JSON download\">";
                $menu .= '</a>&nbsp;';

                $menu .= '<a class="table-button-edit" href="?module=report&action=edit&id=%value%" data-report-id="%value%">';
                $menu .= "<img src=\"themes/$uiTheme/images/edit.svg\" alt=\"edit report\">";
                $menu .= '</a>&nbsp;';

                $menu .= '<a class="table-button-delete" href="#" data-report-id="%value%">';
                $menu .= "<img src=\"themes/$uiTheme/images/delete.svg\" alt=\"delete report\">";
                $menu .= '</a>&nbsp;';

                $menu .= '<a class="table-button-copy" href="#" data-report-id="%value%">';
                $menu .= "<img src=\"themes/$uiTheme/images/copy.svg\" alt=\"copy report\">";
                $menu .= '</a>';

                $menu .= '</td>';
                $menu .= '</tr>';
                $menu .= '</table>';

                $where = ' r.id > 0';

                $sql = "SELECT SQL_CALC_FOUND_ROWS r.id, r.name, r.category, r.description, p.abkuerzung,
                           'ja' AS `public`, r.id 
                        FROM `report` AS `r`
                        LEFT JOIN `projekt` AS `p` ON r.project = p.id";

                break;

            case 'report_table':
                $allowed['report'] = ['table'];

                /** @var Request $request */
                $request = $this->app->Container->get('Request');
                $id = $request->post->getInt('report_id');
                if($id === 0) {
                    $id = (int)$request->get->getInt('id');
                }
                if($id === 0) {
                    $id = (int)$this->app->Secure->GetGET('id');
                }

                /** @var ReportGateway $gateway */
                $gateway = $this->app->Container->get('ReportGateway');
                /** @var ReportService $service */
                $service = $this->app->Container->get('ReportService');
                /** @var ReportColumnFormatter $columnFormatter */
                $columnFormatter = $this->app->Container->get('ReportColumnFormatter');
                try {
                    $report = $gateway->getReportById($id);
                } catch (Exception $e) {
                    $report = null;
                }

                if ($report === null) {
                    $this->app->Tpl->Add('MESSAGE', '<div class="error">Kein Bericht ausgewählt.</div>');
                    $report = new ReportData('empty');
                    $dummyColumns = new ReportColumnCollection([new ReportColumn('empty', 'undefined')]);
                    $report->setColumns($dummyColumns);
                }

                $alignright = [];
                $aligncenter = [];
                $alignleft = [];
                $sumcolumns = [];
                $numbercols = [];
                $heading = [];
                $findcols = [];
                $findcols2 = [];
                $innerTabs = [];
                $weitereswhere = [];
                $width = [];

                $i = 1;
                $columns = $report->getColumns();
                foreach ($columns as $col) {

                    $heading[] = $col->getTitle();
                    $findcols[] = sprintf('reporttable.tab%s', $i);
                    $findcolName = sprintf('tab%s', $i);
                    $formattedFindCol = $columnFormatter->formatColumnExpression($col, $findcolName);
                    $findcols2[$col->getKey()] = $formattedFindCol;
                    $dummy[] = sprintf("'' as tab%s", $i);
                    $width[] = $col->getWidth();
                    $weitereswhere[] = sprintf("tab%s!=''", $i);
                    if ($col->isSumColumn()) {
                        $sumcolumns[] = $i;
                    }
                    if ($col->getSorting() !== ReportColumn::SORT_ALPHABETIC) {
                        $numbercols[] = $i;
                        $innerTabs[$col->getKey()] = sprintf('0 as tab%s', $i);
                    } else {
                        $innerTabs[$col->getKey()] = sprintf("'' as tab%s", $i);
                    }

                    switch ($col->getAlignment()) {
                        case ReportColumn::ALIGN_RIGHT:
                            $alignright[] = $i;
                            break;

                        case ReportColumn::ALIGN_LEFT:
                            $alignleft[] = $i;
                            break;

                        default:
                            $aligncenter[] = $i;
                    }

                    $i++;
                }
                $heading[] = '';
                $width[] = '1%';

                if (count($sumcolumns)) {
                    $sumcol = $sumcolumns;
                }

                $previousString = base64_decode($this->app->Secure->GetGET('postfix'));
                $previousData = json_decode($previousString, true);
                $currentData = [];

                $params = $report->getParameters();
                if ($params !== null) {
                    $getParams = $request->get->all();
                    $getParams = array_change_key_case($getParams, CASE_LOWER);
                    foreach ($params as $param) {
                        $varname = strtolower($param->getVarname());
                        if (array_key_exists($varname, $getParams)) {
                            $currentData[$varname] = $getParams[$varname];
                        } else {
                            $currentData[$varname] = $previousData[$varname];
                        }
                    }
                }

                $currentString = base64_encode(json_encode($currentData));
                $this->app->Secure->GET['postfix'] = $currentString;

                /** @var ReportResolveParameterService $resolver */
                $resolver = $this->app->Container->get('ReportResolveParameterService');
                $report = $resolver->resolveInputParameters($report, $currentData);
                $report = $resolver->resolveInputParameters($report, $request->get->all());

                $queryString = $service->resolveParameters($report);
                //fix possible semicolon
                if (preg_match('/^([^;]+)(\s*;\s*)$/', $queryString, $parts) && count($parts) > 1) {
                    $queryString = $parts[1];
                }
                if (!$service->isSqlStatementAllowed($queryString)) {
                  $this->app->Tpl->Add(
                    'MESSAGE',
                    '<div class="error">Der Bericht enthält eine unerlaubte SQL-Abfrage.</div>'
                  );
                  break;
                }
                /** @var Database $db */
                $db = $this->app->Container->get('Database');
                //make a pre-query to the database to get the order of returned fields
                $preQueryResult = $db->fetchRow($queryString);
                $resultKeys = array_keys($preQueryResult);
                $innerTabsSorted = [];
                //rearrange the findcols2 array so it will match the order in the live table view
                $areColumnsMatchable = true;
                foreach ($resultKeys as $resultKey) {
                  if(isset($innerTabs[$resultKey])){
                    $innerTabsSorted[] = $innerTabs[$resultKey];
                  }
                  else{
                    $areColumnsMatchable = false;
                  }
                }
                if(!$areColumnsMatchable){

                  $msg =
                    '{|Es ist war nicht möglich die Spalten aus dem SQL mit den konfigurierten Spaltennamen abzugleichen. 
                    Wurde das SQL Statement verändert ohne die Spalten anzupassen?
                    Hilfreich könnte auch sein, den Spalten im SQL Statement Aliasse zu geben 
                    und anschließend die Spaltennamen neu zu erzeugen.|}';

                  $this->app->Tpl->Add(
                    'MESSAGE',
                    '<div class="error">'.$msg.'</div>'
                  );
                }

                $sql = 'SELECT SQL_CALC_FOUND_ROWS ' . $findcols[0] . ',' . implode(',',
                        $findcols2) . ',' . $findcols[0] . ' FROM ((SELECT ' . implode(',',
                    $innerTabsSorted) . " LIMIT 0) UNION ALL SELECT * FROM ($queryString) AS `content`) AS `reporttable`";

                $findcols[] = $findcols[0];
                $searchsql = $findcols;
                $defaultorder = 1;
                $defaultorderdesc = 0;

                $where = '(' . implode(' OR ', $weitereswhere) . ')';

                $compiledQuery = sprintf('%s WHERE %s', $sql, $where);

                if (!$service->isSqlStatementAllowed($compiledQuery)) {
                    $sql = '';
                    $where = '';
                    $this->app->Tpl->Add(
                        'MESSAGE',
                        '<div class="error">Der Bericht enthält eine unerlaubte SQL-Abfrage.</div>'
                    );
                }

                break;

            case 'report_shareduser':
                $allowed['report'] = ['share'];

                $heading = [
                    'Benutzer',
                    'Graph',
                    'Datei',
                    'Akt.-Menü',
                    'Tab',
                    'Men&uuml;',
                ];
                $width = ['30%', '5%', '5%', '5%', '5%', '1%'];
                $findcols = [
                    'ru.name',
                    'chart_enabled',
                    'file_enabled',
                    'menu_enabled',
                    'tab_enabled',
                ];
                $searchsql = [
                    'ru.name',
                ];
                $defaultorder = 1;
                $defaultorderdesc = 0;
                $orderby = '';
                $disablebuttons = true;
                $maxrows = 5;
                $aligncenter = [2,3,4,5];
                $filtercols = '';
                $filtercols2 ='';

                $uiTheme = $app->Conf->WFconf['defaulttheme'];

                $menu = '<table cellpadding=0 cellspacing=0>';
                $menu .= '<tr>';
                $menu .= '<td nowrap>';

                $menu .= '<a class="table-button-edit" data-id="%value%">';
                $menu .= "<img src=\"themes/$uiTheme/images/edit.svg\" border=\"0\">";
                $menu .= '</a>';

                $menu .= '<a class="table-button-delete" data-id="%value%">';
                $menu .= "<img src=\"themes/$uiTheme/images/delete.svg\" border=\"0\">";
                $menu .= '</a>&nbsp;';

                $menu .= '</td>';
                $menu .= '</tr>';
                $menu .= '</table>';



                $request = $app->Container->get('Request');
                $where = sprintf(' ru.report_id = %s', $request->get->getInt('id', 0));

                $sql = "SELECT SQL_CALC_FOUND_ROWS ru.id, ru.name,
                           IF(ru.chart_enabled = 1, 'Ja', 'Nein') as `chart_enabled`,
                           IF(ru.file_enabled = 1, 'Ja', 'Nein') as `file_enabled`,
                           IF(ru.menu_enabled = 1, 'Ja', 'Nein') as `menu_enabled`,
                           IF(ru.tab_enabled = 1, 'Ja', 'Nein') as `tab_enabled`, ru.id 
                        FROM `report_user` AS `ru`";

                break;

            case 'report_columns':
                $allowed['report'] = ['edit'];

                /** @var Request $request */
                $request = $this->app->Container->get('Request');
                $id = $request->get->getInt('id');

                $heading = [
                    'Spaltenname SQL',
                    'Bezeichnung',
                    'Spaltenbreite',
                    'Ausrichtung',
                    'Sortierung',
                    'Formatierung',
                    'Spalte summieren',
                    'Reihenfolge',
                    '',
                ];
                $width = ['10%', '10%', '5%', '5%', '5%', '5%', '5%', '5%', '1%'];
                $findcols = [
                    'c.key_name',
                    'c.title',
                    'c.width',
                    'c.alignment',
                    'c.sorting',
                    'c.format_type',
                    'c.sum',
                    'c.sequence'
                ];
                $searchsql = [
                    'c.key_name',
                    'c.title',
                ];
                $defaultorder = 1;
                $defaultorderdesc = 0;
                $orderby = '';
                $disablebuttons = true;
                $filtercols = '';
                $filtercols2 ='';

                $uiTheme = $app->Conf->WFconf['defaulttheme'];

                $menu = '<table cellpadding=0 cellspacing=0>';
                $menu .= '<tr>';
                $menu .= '<td nowrap>';

                $menu .= '<a class="table-button-col-edit" data-column-id="%value%">';
                $menu .= "<img src=\"themes/$uiTheme/images/edit.svg\" border=\"0\">";
                $menu .= '</a>';

                $menu .= '<a class="table-button-col-delete" data-column-id="%value%">';
                $menu .= "<img src=\"themes/$uiTheme/images/delete.svg\" border=\"0\">";
                $menu .= '</a>&nbsp;';

                $menu .= '</td>';
                $menu .= '</tr>';
                $menu .= '</table>';

                $where = sprintf(' c.report_id = %s', $id);

                $sql = "SELECT SQL_CALC_FOUND_ROWS c.id, c.key_name, c.title, c.width, 
                           IF(c.alignment = 'center', 'Mitte',
                               IF(c.alignment = 'left', 'Links',
                                   IF(c.alignment = 'right', 'Rechts', ''))) as `alignment`,
                           IF(c.sorting = 'numeric', 'numerisch',
                               IF(c.sorting = 'alphabetic', 'alphabetisch', 'numerisch')
                               ) as `sorting`,
                           CASE c.format_type
                             WHEN 'sum_money_de' THEN 'Geldbetrag (DE)'
                             WHEN 'sum_money_en' THEN 'Geldbetrag (EN)'
                             WHEN 'date_dmy' THEN 'Datum (dd.mm.YYYY)'
                             WHEN 'date_ymd' THEN 'Datum (YYYY.mm.dd)'
                             WHEN 'date_dmyhis' THEN 'Datum und Uhrzeit (dd.mm.YYYY HH:ii:ss)'
                             WHEN 'date_ymdhis' THEN 'Datum und Uhrzeit (YYYY.mm.dd HH:ii:ss)'
                             WHEN 'custom' THEN c.format_statement
                             ELSE 'Keine'
                           END AS `format_type`,
                           IF(c.sum = 1, 'Ja', 'Nein') as `sum`, c.sequence, c.id 
                        FROM `report_column` AS `c`";
                break;

        }

        $erg = [];

        foreach ($erlaubtevars as $k => $v) {
            if (isset($$v)) {
                $erg[$v] = $$v;
            }
        }

        return $erg;
    }

    /**
     * @return void
     */
    public function Install()
    {
        $this->app->erp->CheckTable('report');
        $this->app->erp->CheckColumn('id', 'int(11)', 'report', 'NOT NULL AUTO_INCREMENT');
        $this->app->erp->CheckColumn('name', 'varchar(255)', 'report', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('description', 'text', 'report', 'NULL');
        $this->app->erp->CheckColumn('project', 'int(11)', 'report', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('sql_query', 'text', 'report', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('remark', 'text', 'report', 'NULL');
        $this->app->erp->CheckColumn('category', 'varchar(255)', 'report', 'NULL');
        $this->app->erp->CheckColumn('readonly', 'tinyint', 'report', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('csv_delimiter', 'varchar(32)', 'report');
        $this->app->erp->CheckColumn('csv_enclosure', 'varchar(32)', 'report');
        $this->app->erp->CheckAlterTable("ALTER TABLE `report` CHANGE `description` `description` TEXT NOT NULL DEFAULT ''");

        $this->app->erp->CheckTable('report_column');
        $this->app->erp->CheckColumn('id', 'int(11)', 'report_column', 'NOT NULL AUTO_INCREMENT');
        $this->app->erp->CheckColumn('report_id', 'int(11)', 'report_column', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('key_name', 'varchar(255)', 'report_column', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('title', 'varchar(255)', 'report_column', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('width', 'varchar(255)', 'report_column', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('alignment', 'varchar(255)', 'report_column', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('sorting', 'varchar(255)', 'report_column', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('sum', 'tinyint', 'report_column', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('sequence', 'int(11)', 'report_column', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('format_type', 'varchar(64)', 'report_column');
        $this->app->erp->CheckColumn('format_statement', 'varchar(255)', 'report_column');

        $this->app->erp->CheckTable('report_parameter');
        $this->app->erp->CheckColumn('id', 'int(11)', 'report_parameter', 'NOT NULL AUTO_INCREMENT');
        $this->app->erp->CheckColumn('report_id', 'int(11)', 'report_parameter', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('varname', 'varchar(255)', 'report_parameter', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('displayname', 'varchar(255)', 'report_parameter', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('description', 'varchar(255)', 'report_parameter', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('default_value', 'varchar(255)', 'report_parameter', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('options', 'varchar(255)', 'report_parameter', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('control_type', 'varchar(255)', 'report_parameter', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('editable', 'tinyint', 'report_parameter', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('variable_extern', 'varchar(255)', 'report_parameter', "NOT NULL DEFAULT ''");

        $this->app->erp->CheckTable('report_transfer');
        $this->app->erp->CheckColumn('id', 'int(11)', 'report_transfer', 'NOT NULL AUTO_INCREMENT');
        $this->app->erp->CheckColumn('report_id', 'int(11)', 'report_transfer', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('ftp_active', 'tinyint', 'report_transfer', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('ftp_type', 'varchar(255)', 'report_transfer', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('ftp_host', 'varchar(255)', 'report_transfer', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('ftp_port', 'varchar(255)', 'report_transfer', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('ftp_user', 'varchar(255)', 'report_transfer', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('ftp_password', 'varchar(255)', 'report_transfer', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('ftp_interval_mode', 'varchar(255)', 'report_transfer', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('ftp_interval_value', 'varchar(255)', 'report_transfer', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('ftp_passive', 'tinyint', 'report_transfer', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('ftp_daytime', 'time', 'report_transfer', 'NULL');
        $this->app->erp->CheckColumn('ftp_format', 'varchar(255)', 'report_transfer', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('ftp_filename', 'varchar(255)', 'report_transfer', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('ftp_last_transfer', 'datetime', 'report_transfer', 'NULL');
        $this->app->erp->CheckColumn('email_active', 'tinyint', 'report_transfer', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('email_recipient', 'text', 'report_transfer', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('email_subject', 'varchar(255)', 'report_transfer', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('email_interval_mode', 'varchar(255)', 'report_transfer', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('email_interval_value', 'varchar(255)', 'report_transfer', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('email_daytime', 'time', 'report_transfer', 'NULL');
        $this->app->erp->CheckColumn('email_format', 'varchar(255)', 'report_transfer', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('email_filename', 'varchar(255)', 'report_transfer', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('email_last_transfer', 'datetime', 'report_transfer', 'NULL');
        $this->app->erp->CheckColumn('url_format', 'varchar(255)', 'report_transfer', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('url_begin', 'date', 'report_transfer', 'NULL');
        $this->app->erp->CheckColumn('url_end', 'date', 'report_transfer', 'NULL');
        $this->app->erp->CheckColumn('url_address', 'text', 'report_transfer', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('url_token', 'text', 'report_transfer', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('api_active', 'tinyint', 'report_transfer', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('api_account_id', 'int(11)', 'report_transfer', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('api_format', 'varchar(255)', 'report_transfer', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckAlterTable("ALTER TABLE `report_transfer` CHANGE `url_address` `url_address` TEXT NOT NULL DEFAULT ''");

        $this->app->erp->CheckTable('report_share');
        $this->app->erp->CheckColumn('id', 'int(11)', 'report_share', 'NOT NULL AUTO_INCREMENT');
        $this->app->erp->CheckColumn('report_id', 'int(11)', 'report_share', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('chart_public', 'tinyint', 'report_share', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('chart_axislabel', 'varchar(255)', 'report_share', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('chart_type', 'varchar(255)', 'report_share', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('chart_x_column', 'varchar(255)', 'report_share', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('data_columns', 'varchar(255)', 'report_share', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('chart_group_column', 'varchar(255)', 'report_share', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('chart_dateformat', 'varchar(255)', 'report_share', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('chart_interval_value', 'int(11)', 'report_share', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('chart_interval_mode', 'varchar(255)', 'report_share', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('file_public', 'tinyint', 'report_share', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('file_pdf_enabled', 'tinyint', 'report_share', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('file_csv_enabled', 'tinyint', 'report_share', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('file_xls_enabled', 'tinyint', 'report_share', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('menu_public', 'tinyint', 'report_share', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('menu_doctype', 'varchar(255)', 'report_share', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('menu_label', 'varchar(255)', 'report_share', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('menu_format', 'varchar(255)', 'report_share', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('tab_public', 'tinyint', 'report_share', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('tab_module', 'varchar(255)', 'report_share', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('tab_action', 'varchar(255)', 'report_share', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('tab_label', 'varchar(255)', 'report_share', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('tab_position', 'varchar(255)', 'report_share', "NOT NULL DEFAULT ''");

        $this->app->erp->CheckTable('report_user');
        $this->app->erp->CheckColumn('id', 'int(11)', 'report_user', 'NOT NULL AUTO_INCREMENT');
        $this->app->erp->CheckColumn('report_id', 'int(11)', 'report_user', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('user_id', 'int(11)', 'report_user', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('name', 'varchar(255)', 'report_user', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('chart_enabled', 'tinyint', 'report_user', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('file_enabled', 'int(11)', 'report_user', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('menu_enabled', 'int(11)', 'report_user', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('tab_enabled', 'int(11)', 'report_user', 'NOT NULL DEFAULT 0');

        $this->app->erp->CheckTable('report_favorite');
        $this->app->erp->CheckColumn('id', 'int(11)', 'report_favorite', 'NOT NULL AUTO_INCREMENT');
        $this->app->erp->CheckColumn('report_id', 'int(11)', 'report_favorite', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('user_id', 'int(11)', 'report_favorite', 'NOT NULL DEFAULT 0');

        $this->installJsonReports();

        $this->app->erp->CheckProzessstarter('Berichte FTP Übertragung (neues Modul)', 'periodisch', '1', '', 'cronjob', 'report_transfer_ftp', 1);
        $this->app->erp->RegisterHook('ajax_filter_hook1','report','AjaxAutocompleteFilterUser');

        //Hooks for Document Module's action menus
        $this->app->erp->RegisterHook('Angebot_Aktion_option', 'report', 'addReportToDocumentActionMenu');
        $this->app->erp->RegisterHook('Angebot_Aktion_case', 'report', 'addDocumentActionMenuCase');

        $this->app->erp->RegisterHook('Auftrag_Aktion_option', 'report', 'addReportToDocumentActionMenu');
        $this->app->erp->RegisterHook('Auftrag_Aktion_case', 'report', 'addDocumentActionMenuCase');

        $this->app->erp->RegisterHook('Gutschrift_Aktion_option', 'report', 'addReportToDocumentActionMenu');
        $this->app->erp->RegisterHook('Gutschrift_Aktion_case', 'report', 'addDocumentActionMenuCase');

        $this->app->erp->RegisterHook('Rechnung_Aktion_option', 'report', 'addReportToDocumentActionMenu');
        $this->app->erp->RegisterHook('Rechnung_Aktion_case', 'report', 'addDocumentActionMenuCase');

        $this->app->erp->RegisterHook('Lieferschein_Aktion_option', 'report', 'addReportToDocumentActionMenu');
        $this->app->erp->RegisterHook('Lieferschein_Aktion_case', 'report', 'addDocumentActionMenuCase');

        $this->app->erp->RegisterHook('Bestellung_Aktion_option', 'report', 'addReportToDocumentActionMenu');
        $this->app->erp->RegisterHook('Bestellung_Aktion_case', 'report', 'addDocumentActionMenuCase');

        $this->app->erp->RegisterHook('Produktion_Aktion_option', 'report', 'addReportToDocumentActionMenu');
        $this->app->erp->RegisterHook('Produktion_Aktion_case', 'report', 'addDocumentActionMenuCase');

        $this->app->erp->removeHookRegister('parse_menu', 'report', 'parseMenuHook');
        $this->app->erp->RegisterHook('player_run_before_include_js_css', 'report', 'beforeJsCssHook');
    }

    public function beforeJsCssHook()
    {
      if(empty($this->app->erp->menuquery)) {
        return;
      }
      $module = $this->app->Secure->GetGET('module');
      if(empty($module) || $module === 'report') {
        return;
      }
      if(!$this->app->erp->RechteVorhanden('report', 'table')) {
        return;
      }
      $action = $this->app->Secure->GetGET('action');
      $id = (int)$this->app->Secure->GetGET('id');
      /** @var ReportGateway $gateway */
      try {
        $gateway = $this->app->Container->get('ReportGateway');
        if($gateway === null){
          return;
        }
        $reports = $gateway->findShareByModuleAction($this->app->User->GetID(), $module, $action);
      }
      catch(Exception $e) {
        return;
      }
      if(empty($reports)) {
        return;
      }

      $nextInd = 0;
      foreach ($this->app->erp->menuquery as $menuItem) {
        if($menuItem['ind'] >= $nextInd){
          $nextInd = $menuItem['ind'] + 1;
        }
      }
      $inserted = false;
      foreach ($reports as $report) {
        $link = 'index.php?module=report&action=view&id=' .
          $report['report_id']
          .'&smodule='
          .$module.'&saction='.$action. ($id > 0 ? '&sid=' . $id : '');
        foreach ($this->app->erp->menuquery as $menuItem) {
          if($menuItem['_link'] === $link){
            continue 2;
          }
        }
        $this->app->erp->menuquery[] = [
          'link' => '<li><a class="reportpopup" data-reportid="'.$report['report_id'].'" href="#">'
            . htmlspecialchars($report['tab_label'])
            . '</a></li>',
          '_link' => $link,
          'ind' => $nextInd,
        ];
        $nextInd++;
        $inserted = true;
      }
      if(!$inserted) {
        return;
      }
      $this->app->ModuleScriptCache->IncludeJavascriptFiles(
        $module,
        [
          'body' => ['./classes/Modules/Report/www/js/report_menu_popup.js']
        ]
      );

      $this->app->Tpl->Parse('PAGE', 'report_menupopup.tpl');
    }

    /**
     * @param array $menu
     */
    public function parseMenuHook(&$menu, $backlink) {

    }

    /**
     * Hook for Autocomplete of shared users
     *
     * @param string $filtername
     * @param array  $newarr
     * @param string $term
     * @param string $term2
     * @param string $term3
     */
    public function AjaxAutocompleteFilterUser($filtername, &$newarr, $term, $term2, $term3)
    {
        if($filtername === 'user_share'){
            $sql = sprintf("SELECT DISTINCT CONCAT(u.id,': ',a.name) as `name` 
                FROM `user` AS `u` JOIN `adresse` AS `a` ON a.id = u.adresse
                WHERE u.activ = 1 AND (u.username LIKE '%%%1\$s%%' OR a.name LIKE '%%%1\$s%%') ", $term);
            $arr = $this->app->DB->SelectArr($sql);
            if(empty($arr)) {
                return;
            }
            foreach($arr as $row) {
                $newarr[] = $row['name'];
            }
        }

        if($filtername === 'report_category'){
            $sql = sprintf("SELECT DISTINCT r.category as `category` 
                FROM `report` AS `r` 
                WHERE r.category LIKE '%%%1\$s%%'", $term);
            $arr = $this->app->DB->SelectArr($sql);
            if(empty($arr)) {
                return;
            }
            foreach($arr as $row) {
                $newarr[] = $row['category'];
            }
        }
    }

    /**
     * @param $id
     * @param $projectStatus
     * @param $option
     */
    public function addReportToDocumentActionMenu($id, $projectStatus, &$option)
    {
        //if(!$this->app->erp->RechteVorhanden('berichte','csv') && !$this->app->erp->RechteVorhanden('berichte','pdf')){
        //    return;
        //}
        if (!$this->app->Container->has('ReportGateway')) {
            return;
        }
        /** @var ReportGateway $gateway */
        $gateway = $this->app->Container->get('ReportGateway');
        $module = $this->app->Secure->GetGET('module');
        $data = $gateway->getDocumentAddActionMenuData($module, $this->app->User->GetID());
        if(empty($data)) {
            return;
        }
        foreach($data as $row) {
            $name = $row['menu_label'];
            if ($name === '') {
                $name = $row['name'];
            }
            $newOption = sprintf('<option value="report_%s">%s</option>', $row['id'], $name);
            $option .= $newOption;
        }
    }

    /**
     * @param $id
     * @param $projectStatus
     * @param $case
     */
    public function addDocumentActionMenuCase($id, $projectStatus, &$case)
    {
        //if(!$this->app->erp->RechteVorhanden('berichte','csv') && !$this->app->erp->RechteVorhanden('berichte','pdf')){
        //    return;
        //}
        if (!$this->app->Container->has('ReportGateway')) {
            return;
        }
        /** @var ReportGateway $gateway */
        $gateway = $this->app->Container->get('ReportGateway');
        $module = $this->app->Secure->GetGET('module');
        $data = $gateway->getDocumentAddActionMenuData($module, $this->app->User->GetID());
        if(empty($data)) {
            return;
        }

        foreach($data as $row) {
            $newCase = sprintf(
                'case \'report_%s\': window.location.href=\'index.php?module=report&action=download&format=%s&id=%s&doctype=%s&docid=%%value%%\'; break;',
                $row['id'],
                $row['menu_format'],
                $row['id'],
                $module
            );

            $case .= $newCase;
        }
    }

    /**
     * Actionhandler for all List like action
     */
    public function HandleActionList()
    {
        $cmd = $this->request->getGet('cmd', '');
        switch ($cmd) {
            case '':
                $this->ReportTiles();
                break;
            case 'ajaxTiles':
                $this->ajaxGetTiles();
                break;
            case 'ajaxGetInputParameters':
                $this->ajaxGetInputParameters();
                break;
            case 'getchart':
                $chart = $this->ajaxGetChart();
                $response = new JsonResponse($chart);
                $response->send();
                $this->app->ExitXentral();
                break;
            case 'ajaxGetFavorite':
            case 'ajaxSetFavorite':
                $this->ajaxFavorite();
                break;
            default:
                throw new BadRequestException(sprintf('Bad request command "%s', $cmd));
        }
    }

    /**
     * Actionhandler for all Edit like action
     */
    public function HandleActionEdit()
    {
        $cmd = $this->request->getGet('cmd', '');
        switch ($cmd) {
            case '':
                $this->ReportEdit();
                break;
            case 'ajaxGetParam':
                $this->ajaxGetParameter();
                break;
            case 'ajaxSaveParam':
                $this->ajaxSaveParameter();
                break;
            case 'ajaxDeleteParam':
                $this->ajaxDeleteParam();
                break;
            case 'ajaxGetColumn':
                $this->ajaxGetColumn();
                break;
            case 'ajaxSaveColumn':
                $this->ajaxSaveColumn();
                break;
            case 'ajaxDeleteColumn':
                $this->ajaxDeleteColumn();
                break;
            case 'ajaxTryQuery':
                $this->ajaxTryQuery();
                break;
            case 'ajaxAutoCreateColumns':
                $this->ajaxAutoCreateColumns();
                break;
            case 'ajaxCopyReport':
                $this->copyReport();
            break;
            default:
                throw new BadRequestException(sprintf('Bad request command "%s', $cmd));
        }
    }

    /**
     * Actionhandler for all Delete like action
     */
    public function HandleActionDelete()
    {
        $cmd = $this->request->getGet('cmd', '');
        switch ($cmd) {
            case '':
                $this->ReportDelete();
                break;
            case 'ajaxDeleteReport':
                $this->ajaxReportDelete();
                break;
            default:
                throw new BadRequestException(sprintf('Bad request command "%s"', $cmd));
        }
    }

    /**
     * Actionhandler for all share like action
     */
    public function HandleActionShare()
    {
        $cmd = $this->request->getGet('cmd', '');
        switch ($cmd) {
            case '':
                $this->ReportShare();
                break;
            case 'ajaxGetShareUser':
            case 'ajaxSaveShareUser':
            case 'ajaxDeleteShareUser':
                $this->ajaxReportUser();
                break;
            default:
                throw new BadRequestException(sprintf('Bad request command "%s"', $cmd));
        }
    }

    /**
     * Actionhandler for all create like action
     */
    public function HandleActionCreate()
    {
        $cmd = $this->request->getGet('cmd', '');
        switch ($cmd) {
            case '':
            case 'getreport':
            case 'savereport':
            case 'deletereport':
                $this->ajaxReportLivetable();
                break;
            case 'ajaxCopyReport':
                $this->ajaxReportCopy();
                break;
            default:
                throw new BadRequestException(sprintf('Bad request command "%s"', $cmd));
        }
    }

  /**
   * @param $formatType
   *
   * @param string $columnName
   * @param string $alias
   *
   * @return string
   */
    protected function createColumnFormat($formatType, $columnName, $alias)
    {
      switch ($formatType) {
        case 'sum_money_de':
          $template = 'FORMAT(%1$s, 2, \'de_DE\') AS `%2$s`';
          break;

        case 'sum_money_en':
          $template = 'FORMAT(%1$s, 2, \'en_EN\') AS `%2$s`';
          break;

        case 'date_dmy':
          $template = 'DATE_FORMAT(%1$s, \'%%d.%%m.%%Y\') AS `%2$s`';
          break;

        case 'date_ymd':
          $template = 'DATE_FORMAT(%1$s, \'%%Y.%%m.%%d\') AS `%2$s`';
          break;

        case 'date_dmyhis':
          $template = 'DATE_FORMAT(%1$s, \'%%d.%%m.%%Y %%H:%%i:%%s\') AS `%2$s`';
          break;

        case 'date_ymdhis':
          $template = 'DATE_FORMAT(%1$s, \'%%Y.%%m.%%d %%H:%%i:%%s\') AS `%2$s`';
          break;

        default:
          $template = '`%2$s`';
      }

      $formattedColumn = sprintf($template, $columnName, $alias);

      return $formattedColumn;
    }

    /**
     * @return array
     */
    protected function ajaxGetChart()
    {
      $id = $this->request->post->getInt('id', 0);
      try{
        $report = $this->gateway->getReportById($id);
        /** @var \Xentral\Modules\Report\ReportChartService $service */
        $service = $this->app->Container->get('ReportChartService');

        $this->app->Tpl->Add('PAGE', $service->renderChartByReport($report));
      }
      catch(Exception $e) {
        $this->renderErrorMessages([$e->getMessage()], $this->app->Tpl, 'PAGE');
      }

      return ['html' => $this->app->Tpl->OutputAsString('report_menupopuptable.tpl')];
    }

    /**
     * @return void
     */
    public function ReportList()
    {
        $this->app->YUI->TableSearch('TAB1', 'report_list', 'show', '', '', basename(__FILE__), __CLASS__);
        $this->renderReportList();
    }

    /**
     * @param string $message
     */
    public function showFailPage($message = '')
    {
        $this->createMenu();

        if ($message !== '') {
            $this->app->Tpl->Add(
                'MESSAGE',
                sprintf('<div class="error">%s</div>', $message)
            );
        }

        $this->template->Parse('PAGE', 'report_fail.tpl');
    }

    /**
     * @return void
     */
    public function ReportTiles()
    {
        $cmd = $this->request->getGet('cmd');
        if($cmd === 'getchart') {
            $json = $this->ajaxGetChart();
            header('Content-Type: application/json');
            echo json_encode($json);
            $this->app->ExitXentral();
        }
        $this->template->Set('TAB1', $this->getTileView());
        $this->renderReportList();
    }

    /**
     * @return void
     */
    public function ajaxGetTiles()
    {
        $filterCategory = $this->request->getPost('filter_category', '');
        $filterTerm = $this->request->getPost('filter_term', '');
        $filterOnlyOwn = $this->request->post->getBool('filter_own', false);
        $filterOnlyFavorites = $this->request->post->getBool('filter_favorites', false);
        try {
            $html= $this->getTileView($filterCategory, $filterTerm, $filterOnlyOwn, $filterOnlyFavorites);
            $response = new JsonResponse(['success' => true, 'html' => $html]);
        } catch (Exception $e) {
            $response = new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response->send();
        $this->app->ExitXentral();
    }

    /**
     * @return void
     */
    public function ajaxFavorite()
    {
        $cmd = $this->request->getGet('cmd', '');
        $reportId = $this->request->post->getInt('id', 0);
        $setFavorite = $this->request->post->getBool('set_favorite', false);
        $userId = $this->app->User->GetID();

        switch ($cmd) {
            case 'ajaxSetFavorite':

                if ($setFavorite === true) {
                    $success = $this->service->addReportFavorite($reportId, $userId);
                } else {
                    $success = $this->service->removeReportFavorite($reportId, $userId);
                }

                //no break;

            case 'ajaxGetFavorite':
                if (!isset($success)) {
                    $success = true;
                }
                $isfavorite = $this->gateway->isFavoriteReportOfUser($reportId, $userId);
                $response = new JsonResponse(['success' => $success, 'is_favorite' => $isfavorite]);
                $response->send();

                break;

            default:
                $response = new Response(
                    'Unknown Server Error',
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
                $response->send();

                break;
        }

        $this->app->ExitXentral();
    }

    /**
     * @param string $category
     * @param string $searchTerm
     *
     * @return string tiles html
     */
    protected function getTileView($category = '', $searchTerm = '', $onlyOwn = false, $onlyFavs = false)
    {
        try {
            $reportList = $this->gateway->getReportList(
                $category,
                $searchTerm,
                $this->app->User->GetID(),
                $onlyOwn,
                $onlyFavs
                );
        } catch (Exception $e) {
            $reportList = [];
        }
        $tiles = $this->app->Container->get('Template');
        $tiles->setDefaultNamespace('Modules/Report');
        $tiles->assign('reportList', $reportList);
        $tiles->assign('theme', 'new');
        return $tiles->fetch('tile_list.tpl');
    }

    protected function renderReportList()
    {
        $this->createMenu();
        $this->template->Set('KURZUEBERSCHRIFT', 'Berichte');
        $this->app->YUI->AutoComplete('reportListFilterCategory', 'report_category');

        if ($this->isUpdateMessageNeedet()) {
            $this->template->Add(
                'MESSAGE',
                '<div class="warning">Willkommen in der neuen Berichte App. Sie können jetzt Ihre Berichte aus der
                      alten App übertragen um die neuen Features für Ihre Berichte zu nutzen. 
                      <a href="?module=berichte&action=list">Zu meinen alten Berichten.</a>
                      Sie können im Bericht den Button "Jetzt übertragen" klicken um den Bericht zu übernehmen.                  
                    </div>'
            );
        }

        $fields = [
            'name' => ['bezeichnung' => 'Name', 'notempty' => 1],
        ];
        $options = [
            'module'               => 'report',
            'action'               => 'create',
            'livetabelle'          => 'report_list',
            'title'                => 'Neuer Bericht',
            'legend'               => '',
            'functionaftersave'    => '',
            'functionbeforedelete' => '',
            'nobearbeiter'         => true,
            'btntarget'            => 'NEWBTN',
            'btnclass'             => 'btnGreenNew',
            'width'                => '600',
            'minwidth'             => '600',
            'onsave'               => 'TileView.reload();'
        ];
        $this->app->YUI->AddSimpleForm('report', $fields, $options, $this);
        $this->template->Parse('PAGE', 'report_list.tpl');
    }

  /**
   * @return array
   */
    protected function ajaxGetReportpopup()
    {
        $reportId = $this->request->getPost('report_id');
        $this->app->Secure->GET['id'] = (int)$reportId;
        try {
            $this->app->YUI->TableSearch('PAGE', 'report_table', 'show', '', '', basename(__FILE__), __CLASS__);
        }
        catch(Exception $e) {
            $this->renderErrorMessages([$e->getMessage()], $this->app->Tpl, 'PAGE');
        }

        return ['html' => $this->app->Tpl->OutputAsString('report_menupopuptable.tpl')];
    }

    /**
     * View Report result as table
     *
     * @return void
     */
    public function ReportTable()
    {
        $this->createMenu();
        $this->app->erp->Headlines('Berichte');
        $cmd = $this->request->getGet('cmd', '');
        if($cmd === 'getreportpopup') {
          $json = $this->ajaxGetReportpopup();
          header('Content-Type: application/json');
          echo json_encode($json);
          $this->app->ExitXentral();
        }
        $id = $this->request->get->getInt('id', 0);
        $this->app->Tpl->Add('REPORT_ID', $id);
        $this->template->Set('KURZUEBERSCHRIFT', '[NAME]');
        $queryId = $this->request->get->getInt('id', 0);
        $report = $this->gateway->getOnlyReportById($queryId);
        if ($report === null) {
            $this->app->Tpl->Set('NAME', 'BERICHTE');
        } else {
            $this->app->Tpl->Set('NAME', $report->getName());
        }

        if ($this->request->getPost('submit_parameters', '') !== '') {
            $params = $this->request->post->all();
            unset($params['submit_parameters']);
            //$url = $this->request->getBaseUrl();
            foreach ($params as $key => $value) {
                if ($value === null || $value === '') {
                    unset($params[$key]);
                }
            }
            $url = sprintf('?module=report&action=view&%s', http_build_query($params));
            $this->redirectTo($url);
            $this->app->ExitXentral();
        }

        try {
            $this->app->YUI->TableSearch('TABLEVIEW', 'report_table', 'show', '', '', basename(__FILE__), __CLASS__);
        }
        catch (ColumnDefinitionException $e) {
            $this->renderErrorMessages(['Die Spaltendefinition in diesem Bericht ist fehlerhaft.'], $this->app->Tpl);
        }
        catch (ReportNoDataException $e) {
            $this->renderErrorMessages(['Keine Daten gefunden.'], $this->app->Tpl);
        }
        catch (Exception $e) {
            $this->app->erp->LogFile('Exception while creating report', $e);
            $this->renderErrorMessages(['Fehler beim Abrufen des Berichts.'], $this->app->Tpl);
        }

        $this->app->Tpl->Add('LOADSCRIPT',
            '<script language="javascript" type="text/javascript"
                          src="../classes/Modules/Report/www/js/report_view.js"></script>'
        );
        $report = $this->gateway->getReportById((int)$id);
        if($report !== null) {
            $this->app->Tpl->Set('KURZUEBERSCHRIFT2', $report->getName());
        }
        $this->renderParameterInputFields($id, $this->request->get->all());
        $this->app->Tpl->Parse('PAGE', 'report_view.tpl');
    }

    /**
     * @return void
     */
    public function ReportEdit()
    {
        $this->createMenu();
        $this->template->Set('KURZUEBERSCHRIFT', '[NAME]');
        $this->template->Set('KURZUEBERSCHRIFT1', 'Bearbeiten');
        $this->app->YUI->AutoComplete('reportEditProject', 'projektname');
        $this->app->YUI->AutoComplete('reportEditCategory', 'report_category');

        //get posted form data
        $form = $this->getReportFormData($this->request);
        $formErrors = $this->getFormErrorMessage($form);
        $readonly = false;

        /** @var FileUpload $upload */
        $upload = $this->request->getFile('submit_import');
        $isUpload = $upload !== null;
        if ($isUpload) {
            $importToId = 0;
            if (isset($form['id']) && $form['id'] > 0) {
                $importToId = $form['id'];
            }

            /** @var ReportJsonImportService $importService */
            $importService = $this->app->Container->get('ReportJsonImportService');
            try {
                $importedId = $importService->importJsonUpload($upload, $importToId);
                $this->redirectTo(sprintf('?module=report&action=edit&id=%s', $importedId));
                $this->app->ExitXentral();
            }
            catch (InvalidArgumentException $e) {
                $this->renderErrorMessages(
                    ['Bitte laden Sie nur Dateien vom Typ JSON hoch.'],
                    $this->template
                );
            }
            catch (JsonParseException $e) {
                $this->renderErrorMessages(
                    ['Die Struktur in der hochgeladenen Datei ist ungültig.'],
                    $this->template
                );
            }
            catch (ReportReadonlyException $e) {
                $this->renderErrorMessages(
                    ['Dieser Bericht ist schreibgeschützt und darf nicht überschrieben werden.'],
                    $this->template
                );
            }
            catch (Exception $e) {
                $this->renderErrorMessages(
                    ['Fehler beim laden des Berichts'],
                    $this->template
                );
            }
        }

        //in case of submit -> save form
        if (!empty($form) && empty($formErrors) && !$isUpload) {
            if ($form['id'] === 0) {
                $form['name'] = $this->service->generateIncrementedReportName($form['name']);
            } else {
                $existingReport = $this->gateway->getOnlyReportById($form['id']);
                if ($existingReport !== null && $form['name'] !== $existingReport->getName()) {
                    $form['name'] = $this->service->generateIncrementedReportName($form['name'], $form['id']);
                }
            }
            $report = ReportData::fromFormData($form);

            if ($this->gateway->isReportReadonly($report->getId())) {
                $this->renderErrorMessages(['Dieser Bericht ist schreibgeschützt'], $this->template);
                $readonly = true;
            } else {
                $newId = $this->service->saveReport($report);
                if ($newId > 0) {
                    $this->redirectTo(sprintf('?module=report&action=edit&id=%s', $newId));
                    $this->app->ExitXentral();
                }
            }
        }

        $columnArray = [];
        $paramArray = [];

        //in case of get -> display report
        $queryId = $this->request->get->getInt('id', 0);
        if ($queryId > 0 && (empty($form) || $this->gateway->isReportReadonly($queryId) === true)) {
            $report = $this->gateway->getReportById($queryId);
            if ($report !== null && $report->isReadonly()) {
                $this->template->Add(
                    'MESSAGE',
                    sprintf('<form action="index.php?module=report&action=edit&cmd=ajaxCopyReport" method="post">
                    <div class="info">Dieser Bericht ist schreibgeschützt. Zum Bearbeiten bitte Kopie anlegen.
                        <input type="hidden" name="id" value="%s">
                        <input type="submit" name="copy" value="Kopie anlegen">           
                    </div>
                  </form>',
                    $queryId
                    )
                );
                $this->template->Set('READONLY', 'readonly');
                $this->template->Set('DISABLED', 'disabled');
            }
            if ($report !== null) {
                $form = $report->getFormData();
                $columns = $report->getColumns();
                $parameters = $report->getParameters();
            } else {
                $this->template->Add(
                    'MESSAGE',
                    sprintf(
                        '<div class="warning">Ein Bericht Mit der Id "%s" wurde nicht gefunden.</div>',
                        $queryId
                    )
                );
                $form = [];
                $columns = null;
                $parameters = null;
            }

            if ($columns === null) {
                $columnArray = [];
            } else {
                $columnArray = $columns->toArray();
            }
            $paramArray = [];
            if ($parameters !== null) {
                foreach ($report->getParameters() as $param) {
                    $paramArray[] = $param->toArray();
                }
            }
        }

        $this->renderForm($form, $this->template);
        $this->renderFormErrors($formErrors, $this->template);
        //$this->renderColumnView($columnArray, $this->template);
        $this->app->YUI->TableSearch('COLUMNTABLE', 'report_columns', 'show', '', '', basename(__FILE__), __CLASS__);
        $this->renderParameterView($paramArray, $this->template);

        $this->template->Set('THEME', 'new');
        $this->template->Parse('PAGE', 'report_edit.tpl');
    }

    public function ReportTransfer()
    {
        $this->createMenu();
        $this->template->Set('KURZUEBERSCHRIFT', '[NAME]');
        $this->template->Set('KURZUEBERSCHRIFT1', 'Übertragen');

        $submitSave = '' !== $this->request->getPost('submit', '');
        $submitCreateUrl = '' !== $this->request->getPost('create_url', '');

        $form = $this->getTransferFormData($this->request);
        if (!empty($form) && $submitSave) {
            $newId = $this->service->saveTransferArray($form);
            if ($newId > 0) {
                $this->redirectTo(sprintf('?module=report&action=transfer&id=%s', $form['report_id']));
                $this->app->ExitXentral();
            }
        }

        if (!empty($form) && $submitCreateUrl) {
            $newId = $this->service->saveTransferArray($form);
            $this->createUrl($form['id'], $form['report_id']);
            if ($form['id'] > 0 || $newId > 0) {
                $this->redirectTo(sprintf('?module=report&action=transfer&id=%s', $form['report_id']));
                $this->app->ExitXentral();
            }
        }

        $queryId = $this->request->get->getInt('id', 0);
        $report = $this->gateway->getOnlyReportById($queryId);
        if ($report === null) {
            $this->app->Tpl->Set('NAME', 'BERICHTE');
        } else {
            $this->app->Tpl->Set('NAME', $report->getName());
        }

        if (empty($form) && $queryId > 0) {
            $form = $this->gateway->findTransferArrayByReportId($queryId);
        }

        if (empty($form)) {
            $form = ['id' => 0];
        }
        $this->renderTransferForm($form, $this->template);

        $this->app->YUI->DatePicker('transferUrlBegin');
        $this->app->YUI->DatePicker('transferUrlEnd');
        $this->app->YUI->TimePicker('transferFtpTime');
        $this->app->YUI->TimePicker('transferEmailTime');
        $this->app->YUI->AutoComplete('transferApiAccount', 'api_account');

        $this->app->erp->checkActiveCronjob('report_transfer_ftp');

        $this->template->Set('THEME', 'new');
        $this->template->Parse('PAGE', 'report_transfer.tpl');
    }

    public function ReportShare()
    {
        $this->createMenu();
        $this->template->Set('KURZUEBERSCHRIFT', '[NAME]');
        $this->template->Set('KURZUEBERSCHRIFT1', 'FREIGABEN');

        $this->app->YUI->AutoComplete('sharedUserFind', 'api_account');
        $this->app->YUI->TableSearch('USERTABLE', 'report_shareduser', 'show', '', '', basename(__FILE__), __CLASS__);

        $form = $this->getShareFormData($this->request);
        if (!empty($form)) {
            $newId = $this->service->saveShareArray($form);
            if ($newId > 0) {
                $this->redirectTo(sprintf('?module=report&action=share&id=%s', $form['report_id']));
                $this->app->ExitXentral();
            }
        }

        $queryId = $this->request->get->getInt('id', 0);
        $report = $this->gateway->getOnlyReportById($queryId);
        if ($report === null) {
            $this->app->Tpl->Set('NAME', 'BERICHTE');
        } else {
            $this->app->Tpl->Set('NAME', $report->getName());
        }
        if (empty($form) && $queryId > 0) {
            $form = $this->gateway->findShareArrayByReportId($queryId);
        }

        if (empty($form)) {
            $form = ['id' => 0];
        }
        $this->renderShareForm($form, $this->template);
        $this->app->YUI->AutoComplete('sharedUserFind', 'user_share');
        $this->app->YUI->AutoComplete('inputDialogUser', 'user_share');

        //$this->template->Set('ID', $queryId);
        $this->template->Set('THEME', 'new');
        $this->template->Set('REPORT_ID', $queryId);
        $this->template->Parse('PAGE', 'report_share.tpl');
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function getShareFormData(Request $request)
    {
        if ($request->getPost('submit', null) === null) {
            return [];
        }

        $data =  [
            'id' => $request->post->getInt('id', 0),
            'report_id' => $request->get->getInt('id', 0),
            'chart_public' => $request->getPost('chart_public', 0),
            'chart_axislabel' => $request->getPost('chart_axislabel', ''),
            'chart_dateformat' => $request->getPost('chart_dateformat', 'line'),
            'chart_type' => $request->getPost('chart_type', ''),
            'chart_x_column' => $request->getPost('chart_x_column', ''),
            'data_columns' => $request->getPost('data_columns', ''),
            'chart_group_column' => $request->getPost('chart_group_column', ''),
            'chart_interval_value' => (int)$request->getPost('chart_interval_value', 0),
            'chart_interval_mode' => $request->getPost('chart_interval_mode', ''),
            'file_public' => $request->getPost('file_public', 0),
            'file_pdf_enabled' => $request->getPost('file_pdf_enabled', 0),
            'file_csv_enabled' => $request->getPost('file_csv_enabled', 0),
            'file_xls_enabled' => $request->getPost('file_xls_enabled', 0),
            'menu_public' => $request->getPost('menu_public', 0),
            'menu_doctype' => $request->getPost('menu_doctype', ''),
            'menu_label' => $request->getPost('menu_label', ''),
            'menu_format' => $request->getPost('menu_format', ''),
            'tab_public' => $request->getPost('tab_public', 0),
            'tab_module' => $request->getPost('tab_module', ''),
            'tab_action' => $request->getPost('tab_action', ''),
            'tab_label' => $request->getPost('tab_label', ''),
            'tab_position' => $request->getPost('tab_position', ''),
        ];

        $checkboxes = [
            'chart_public',
            'file_public',
            'file_pdf_enabled',
            'file_csv_enabled',
            'file_xls_enabled',
            'menu_public',
            'tab_public',
        ];
        foreach ($checkboxes as $checkbox) {
            if (isset($data[$checkbox]) && $data[$checkbox] === 'on') {
                $data[$checkbox] = 1;
            }
        }

        return $data;
    }

    /**
     * @param array          $form
     * @param TemplateParser $template
     */
    protected function renderShareForm($form, TemplateParser $template)
    {
        $report = $this->gateway->getReportById((int)$form['report_id']);
        if($report !== null) {
            $template->Set('KURZUEBERSCHRIFT2', $report->getName());
        }
        foreach ($form as $key => $value) {
            $template->Set(strtoupper($key), $value);
        }
        $checkboxes = [
            'chart_public',
            'file_public',
            'file_pdf_enabled',
            'file_csv_enabled',
            'file_xls_enabled',
            'menu_public',
            'tab_public',
        ];
        foreach ($checkboxes as $checkbox) {
            if (isset($form[$checkbox]) && $form[$checkbox] === 1) {
                $template->Set(sprintf('%s_CHECKED', strtoupper($checkbox)), 'checked');
            }
        }

        $template->Set(
            'CHART_TYPE_OPTIONS',
            $this->renderOptions([
                'line'      => 'Liniendiagramm',
                'bar'       => 'Balkendiagramm',
                'pie'       => 'Tortendiagramm',
                'donought'  => 'Donought',
                'radar'     => 'Radar',
                'polarArea' => 'Polar',
            ],
                $form['chart_type'])
        );

        // Document types where an action menu can be added
        $template->Set(
            'CHART_DATEFORMAT_OPTIONS',
            $this->renderOptions([
                'Y-m-d H:i:s' => 'Y-m-d H:i:s',
                'd.m.Y H:i:s' => 'd.m.Y H:i:s',
            ],
                $form['chart_dateformat'])
        );
        //file format for output
        $formatValues = ['csv' => 'CSV', 'pdf' => 'PDF'];
        $template->Set('MENU_FORMAT_OPTIONS', $this->renderOptions($formatValues, $form['menu_format']));

        // Document types where an action menu can be added
        $template->Set(
            'DOCTYPE_OPTIONS',
            $this->renderOptions([
                '' => '{|Keiner|}',
                'angebot' => '{|Angebot|}',
                'auftrag' => '{|Auftrag|}',
                'rechnung' => '{|Rechnung|}',
                'gutschrift' => '{|Gutschrift|}',
                'lieferschein' => '{|Lieferschein|}',
                'bestellung' => '{|Bestellung|}',
                'produktion' => '{|Produktion|}',
            ],
            $form['menu_doctype'])
        );

        // Interval mode for drawing a chart
        $template->Set(
            'CHART_INTERVAL_MODE_OPTIONS',
            $this->renderOptions([
                'day' => '{|Tage|}',
                'week' => '{|Wochen|}',
                'month' => '{|Monate|}',
            ],
            $form['chart_interval_mode'])
        );

        // Modules where a tab can be added
        $template->Set(
            'TAB_MODULE_OPTIONS',
            $this->renderOptions([
                '' => '{|Keines|}',
                'angebot' => '{|Angebot|}',
                'auftrag' => '{|Auftrag|}',
                'rechnung' => '{|Rechnung|}',
                'gutschrift' => '{|Gutschrift|}',
                'lieferschein' => '{|Lieferschein|}',
                'bestellung' => '{|Bestellung|}',
                'produktion' => '{|Produktion|}',
            ],
            $form['tab_module'])
        );

        // Positions where tabs can be added
        $template->Set(
            'TAB_POSITION_OPTIONS',
            $this->renderOptions([
                'nach_freifeld' => '{|nach Freifeld|}',
                'vor_freifeld' => '{|vor Freifeld|}',
            ],
                $form['tab_position'])
        );
    }

    /**
     * @return void
     */
    public function ReportDelete()
    {
        $reportId = $this->request->get->getInt('id');
        if ($this->gateway->isReportReadonly($reportId)) {
            $response = new Response('Report is readonly.', Response::HTTP_FORBIDDEN);
            $response->send();
            $this->app->ExitXentral();
        }
        $this->service->deleteReportById($reportId);
        $this->redirectTo('index.php?module=report&action=list');
        $this->app->ExitXentral();
    }

    /**
     *
     */
    public function ajaxReportLivetable()
    {
        $id = $this->request->post->getInt('id', 0);
        $cmd = $this->request->getGet('cmd', '');
        $userId = $this->app->User->GetID();

        switch ($cmd) {
            case 'getreport':
                $response = new JsonResponse([]);
                $response->send();
                $this->app->ExitXentral();
                break;

            case 'savereport':
                $postName = $this->request->getPost('name', '');
                if ($postName === '') {
                    $response = new Response('Name is required.', Response::HTTP_BAD_REQUEST);
                    $response->send();
                    $this->app->ExitXentral();
                }
                $name = $this->service->generateIncrementedReportName($postName);
                $report = ReportData::fromFormData(['id' => $id, 'name' => $name]);
                $newId = $this->service->saveReport($report);
                $this->service->saveReportUserArray([
                    'report_id' => $newId,
                    'user_id' => $userId,
                    'name' => $this->app->User->GetName(),
                    'chart_enabled' => 0,
                    'file_enabled' => 0,
                    'menu_enabled' => 0,
                    'tab_enabled' => 0,
                ]);
                if ($newId > 0) {
                    $response = new JsonResponse(['id' => $newId]);
                } else {
                    $response = new Response(
                        'Unknown Server error.',
                        Response::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
                $response->send();
                $this->app->ExitXentral();

                break;

            case 'deletereport':
                break;
        }

        $response = new JsonResponse(['success' => false]);
        $response->send();
        $this->app->ExitXentral();
    }

    /**
     * @return void
     */
    public function ajaxReportDelete()
    {
        $reportId = $this->request->post->getInt('id', 0);
        if ($this->gateway->isReportReadonly($reportId)) {
            $response = new Response('Report is readonly.', Response::HTTP_FORBIDDEN);
            $response->send();
            $this->app->ExitXentral();
        }
        try {
            $this->service->deleteReportById($reportId);
            $response = new JsonResponse(['success' => true]);
            $response->send();
        } catch (Exception $e) {
            $response = new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->send();
        }

        $this->app->ExitXentral();
    }

    /**
     * @return void
     */
    public function ReportDownload()
    {
        $this->createMenu();
        $id = $this->request->get->getInt('id', 0);
        $format = $this->request->getGet('format', '');
        $documentId = $this->request->get->getInt('docid', 0);
        $doctype = $this->request->getGet('doctype', '');
        $userId = $this->app->User->GetID();

        $params = $this->request->get->all();
        unset($params['id'], $params['format'], $params['module'], $params['action'], $params['cmd']);

        try {
            $report = $this->gateway->getReportById($id);
        } catch (Exception $e) {
            $report = null;
        }
        if ($report === null) {
            $errorResponse = new Response('report not found', Response::HTTP_NOT_FOUND);
            $errorResponse->send();
            $this->app->ExitXentral();
        }
        if (!$this->service->isSqlStatementAllowed($report->getSqlQuery())) {
            $errorResponse = new Response(
                'sql statement incorrect',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
            $errorResponse->send();
            $this->app->ExitXentral();
        }

        try {
            switch ($format) {
                case 'csv':
                    if (!$this->gateway->userCanDownloadCsv($report->getId(), $userId)) {
                        throw new ReportUserAccessException('No permission for this report.');
                    }
                    /** @var ReportCsvExportService $csvExporter */
                    $csvExporter = $this->app->Container->get('ReportCsvExportService');
                    $filename = $csvExporter->generateFileName($report);
                    $filePath = $csvExporter->createCsvFileFromReport($report, $params);
                    $download = FileResponse::createForcedDownload($filePath, $filename, true);
                    $download->send();
                    $this->app->ExitXentral();

                    break;

                case 'pdf':
                    if (!$this->gateway->userCanDownloadPdf($report->getId(), $userId)) {
                        throw new ReportUserAccessException('No permission for this report.');
                    }
                    /** @var ReportPdfExportService $pdfExporter */
                    $pdfExporter = $this->app->Container->get('ReportPdfExportService');
                    $filename = $pdfExporter->generateFileName($report);
                    $filePath = $pdfExporter->createPdfFileFromReport($report, $params);
                    $download = FileResponse::createForcedDownload($filePath, $filename, true);
                    $download->send();
                    $this->app->ExitXentral();

                    break;

                case 'json':
                    /** @var ReportJsonExportService $jsonExporter */
                    $jsonExporter = $this->app->Container->get('ReportJsonExportService');
                    $filename = $jsonExporter->generateFileName($report);
                    $filePath = $jsonExporter->createJsonFileFromReport($report, $filename);
                    $download = FileResponse::createForcedDownload($filePath, $filename, true);
                    $download->send();
                    $this->app->ExitXentral();

                    break;

                default:
                    $errorResponse = new Response('invalid format', Response::HTTP_BAD_REQUEST);
                    $errorResponse->send();
                    $this->app->ExitXentral();
            }
        }
        catch (ReportUserAccessException $e) {
            $this->showFailPage('Keine Zugriffsberechtigung auf diesen Bericht.');
        }
        catch (Exception $e) {
            $this->showFailPage('Dieser Bericht ist fehlerhaft.');
        }
    }

    /**
     * Demo mode only!
     */
    public function ReportExport()
    {
        $id = (int)$this->request->get->getInt('id', 0);
        $token = $this->request->getGet('cmd', '');

        $transferData = $this->app->DB->SelectRow(
            sprintf(
            'SELECT rt.url_format, rt.url_begin, rt.url_end, rt.url_token, rt.url_address 
                    FROM `report_transfer` AS `rt`
                    JOIN `report` AS `r` ON r.id = rt.report_id
                    WHERE r.id=%s',
                $id
            )
        );
        if(empty($transferData)) {
            $this->redirectTo('?module=report&action=list');
            $this->app->ExitXentral();
        }

        if (empty($transferData['url_address'])) {
            $this->redirectTo('?module=report&action=list');
            $this->app->ExitXentral();
        }

        if ($token !== $transferData['url_token']) {
            $this->redirectTo('?module=report&action=list');
            $this->app->ExitXentral();
        }

        $now = date('Y-m-d');
        $beginDate = date('Y-m-d', strtotime($transferData['url_begin']));
        $endDate = date('Y-m-d', strtotime($transferData['url_end']));
        $checkbegin = true;
        if(!empty($beginDate) && $transferData['url_begin'] !== null && $now < $beginDate) {
            $checkbegin = false;
        }
        $checkend = true;
        if(!empty($endDate) && $transferData['url_end'] !== null && $now > $endDate) {
            $checkend = false;
        }
        if ($checkbegin !== true || $checkend !== true) {
            $this->redirectTo('?module=report&action=list');
            $this->app->ExitXentral();
        }

        $report = $this->gateway->getReportById($id);
        if ($report === null) {
            $this->redirectTo('?module=report&action=list');
            $this->app->ExitXentral();
        }

        switch ($transferData['url_format']) {
            case 'csv':
                /** @var ReportCsvExportService $csvExporter */
                $csvExporter = $this->app->Container->get('ReportCsvExportService');
                $filename = $csvExporter->generateFileName($report);
                $filePath = $csvExporter->createCsvFileFromReport($report);
                $download = FileResponse::createForcedDownload($filePath, $filename, true);
                $download->send();
                $this->app->ExitXentral();

                break;

            case 'pdf':
                /** @var ReportPdfExportService $pdfExporter */
                $pdfExporter = $this->app->Container->get('ReportPdfExportService');
                $filename = $pdfExporter->generateFileName($report);
                $filePath = $pdfExporter->createPdfFileFromReport($report);
                $download = FileResponse::createForcedDownload($filePath, $filename, true);
                $download->send();
                $this->app->ExitXentral();

                break;

            default:
                $this->redirectTo('?module=report&action=list');
                $this->app->ExitXentral();
                break;
        }

    }

    /**
     * @return void
     */
    public function ajaxReportCopy()
    {
        $reportId = $this->request->post->getInt('id', 0);
        $isUi = $this->request->post->has('copy');
        /** @var Response $response */
        try {
            $newId = $this->service->copyReport($reportId);
            $this->service->saveReportUserArray([
                'report_id' => $newId,
                'user_id' => $this->app->User->GetID(),
                'name' => $this->app->User->GetName(),
                'chart_enabled' => 0,
                'file_enabled' => 0,
                'menu_enabled' => 0,
                'tab_enabled' => 0,
            ]);
            if ($isUi) {
                $this->redirectTo(sprintf('?module=report&action=edit&id=%s', $newId));
                $this->app->ExitXentral();
            }
            $response = new JsonResponse(['success' => true, 'id' => $newId]);
        } catch (InvalidArgumentException $e) {
            $response = new Response('Report not Found', Response::HTTP_NOT_FOUND);
        } catch (DatabaseTransactionException $e) {
            $response = new Response('Database transaction error.', Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $e) {
            $response = new Response('Unknown server error.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $response->send();

        $this->app->ExitXentral();
    }

    /**
     * @return void
     */
    public function ajaxGetInputParameters()
    {
        $id = $this->request->post->getInt('id', 0);
        try {
            $params = $this->gateway->getParametersByReportId($id);
            $data = ['success' => true, 'params' => $params];
            $response = new JsonResponse($data, JsonResponse::HTTP_OK);
            $response->send();
        } catch (Exception $e) {
            $response = new Response($e->getMessage(), 500);
            $response->send();
        }
        $this->app->ExitXentral();
    }

    /**
     * @return void
     */
    public function ajaxGetParameter()
    {
        $id = $this->request->post->getInt('id', 0);
        $data = [];
        if ($id > 0) {
            $parameter = $this->gateway->getParameterById($id);
            $data = $parameter->toArray();
        }
        $response = new JsonResponse($data, Response::HTTP_OK);
        $response->send();
        $this->app->ExitXentral();
    }

    /**
     * @return void
     */
    public function ajaxSaveParameter()
    {
        $reportId = $this->request->post->getInt('reportId', 0);
        if ($reportId < 1) {
            $response = new Response(
                'Parameter not associated to a report.',
                Response::HTTP_NOT_ACCEPTABLE
            );
            $response->send();
            $this->app->ExitXentral();
        }

        if ($this->gateway->isReportReadonly($reportId)) {
            $response = new Response(
                'Der Bericht ist schreibgeschützt.',
                Response::HTTP_FORBIDDEN
            );
            $response->send();
            $this->app->ExitXentral();
        }

        $varname = $this->app->DB->real_escape_string($this->request->getPost('varname', ''));
        if ($varname === '') {
            $response = new Response('Variablenname wird benötigt.', Response::HTTP_NOT_ACCEPTABLE);
            $response->send();
            $this->app->ExitXentral();
        }
        $varname = mb_strtoupper($varname);
        if (in_array($varname, $this->parameterNameBlacklist, true)) {
            $response = new Response(
                sprintf('Der Variablenname "%s" ist nicht erlaubt.', $varname),
                Response::HTTP_NOT_ACCEPTABLE
            );
            $response->send();
            $this->app->ExitXentral();
        }

        $defaultValue = $this->app->DB->real_escape_string($this->request->getPost('value', ''));
        if ($defaultValue === '') {
            $response = new Response('Standardwert wird benötigt.', Response::HTTP_NOT_ACCEPTABLE);
            $response->send();
            $this->app->ExitXentral();
        }

        try {
            $optionsString = $this->request->getPost('options', '');
            $options = [];
            if ($optionsString !== '') {
                $options = ReportParameter::parseOptions($optionsString);
            }
        } catch (Exception $e) {
            $response = new Response('Falsche formatierung in Werteauswahl.', Response::HTTP_NOT_ACCEPTABLE);
            $response->send();
            $this->app->ExitXentral();
        }
        try {
            $parameter = new ReportParameter(
                $varname,
                $defaultValue,
                $this->app->DB->real_escape_string($this->request->getPost('label', '')),
                $options,
                $this->app->DB->real_escape_string($this->request->getPost('description', '')),
                $this->request->post->getInt('editable', 0),
                $this->request->post->getInt('paramId', 0),
                $this->app->DB->real_escape_string($this->request->getPost('control_type', ''))
            );
            $newId = $this->service->saveParameter($parameter, $reportId);
            $data = ['success' => true, 'id' => $newId];
            $response = new JsonResponse($data, 200);
            $response->send();
        } catch (Exception $e) {
            $response = new Response($e->getMessage(), 500);
            $response->send();
        }

        $this->app->ExitXentral();
    }

    /**
     * @return void
     */
    public function ajaxDeleteParam()
    {
        $id = $this->request->post->getInt('id', 0);
        if ($id < 1) {
            $response = new Response('Parameter not found.', Response::HTTP_NOT_FOUND);
            $response->send();
            $this->app->ExitXentral();
        }
        $reportId = $this->gateway->getReportIdByParameter($id);
        if ($this->gateway->isReportReadonly($reportId)) {
            $response = new Response('Report is readonly', Response::HTTP_FORBIDDEN);
            $response->send();
            $this->app->ExitXentral();
        }
        try {
            $this->service->deleteParamById($id);
            $response = new JsonResponse(['success' => true], Response::HTTP_OK);
        } catch (Exception $e) {
            $response = new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $response->send();
        $this->app->ExitXentral();
    }

    /**
     * @return void
     */
    public function ajaxGetColumn()
    {
        $id = $this->request->post->getInt('id', 0);
        $data = [];
        if ($id > 0) {
            $column = $this->gateway->getColumnById($id);
            $data = $column->toArray();
        }
        $response = new JsonResponse($data, Response::HTTP_OK);
        $response->send();
        $this->app->ExitXentral();
    }

    public function copyReport()
    {
      $id = $this->request->post->getInt('id', 0);
      if ($id > 0) {
        try {
          $newId = $this->service->copyReport($id);
          $this->app->Location->execute('index.php?module=report&action=edit&id='.$newId);
        }
        catch(Exception $e) {

        }
      }
      $this->app->Location->execute('index.php?module=report&action=edit&id='.$id);
    }

    public function ajaxAutoCreateColumns()
    {
        $id = $this->request->post->getInt('id', 0);
        $report = $this->gateway->getReportById($id);
        if($report === null) {
            $response = new Response('Report not found.', Response::HTTP_NOT_FOUND);
            $response->send();
            $this->app->ExitXentral();
        }
        try {
            $newReport = $this->service->autoCreateColumns($report);
            $newId = $this->service->saveReport($newReport);
            $response = new JsonResponse(['success' => true, 'message' => '']);
        }
        catch (ReportSqlQueryException $e) {
            $response = new JsonResponse([
                'success' => false,
                'message' => 'Bitte erstellen und speichern Sie zuerst ein funktionsfähiges SQL Statement.'
            ]);
        }
        catch (Exception $e) {
            $response = new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response->send();
        $this->app->ExitXentral();
    }

    public function ajaxSaveColumn()
    {
        $reportId = $this->request->post->getInt('reportId', 0);
        if ($reportId < 1) {
            $response = new Response(
                'Column not associated to a report.',
                Response::HTTP_NOT_ACCEPTABLE
            );
            $response->send();
            $this->app->ExitXentral();
        }

        if ($this->gateway->isReportReadonly($reportId)) {
            $response = new Response(
                'Report is readonly.',
                Response::HTTP_FORBIDDEN
            );
            $response->send();
            $this->app->ExitXentral();
        }

        $key = trim($this->request->getPost('key_name', ''));
        if ($key === '') {
            $response = new Response('Column key is required.', Response::HTTP_NOT_ACCEPTABLE);
            $response->send();
            $this->app->ExitXentral();
        }

        $title = trim($this->request->getPost('title', ''));
        if ($title === '') {
            $response = new Response('Column name is required.', Response::HTTP_NOT_ACCEPTABLE);
            $response->send();
            $this->app->ExitXentral();
        }

        try {
            $formatType = $this->app->DB->real_escape_string($this->request->getPost('format_type', null));
            if ($formatType === '') {
              $formatType = null;
            }
            $formatStatement = trim($this->request->getPost('format_statement', null));
            if ($formatType === ReportColumn::FORMAT_CUSTOM) {
                $this->service->validateCustomColumnFormat($formatStatement);
            }
            if ($formatStatement === '') {
                $formatStatement = null;
            }
            $column = new ReportColumn(
                $key,
                $title,
                $this->app->DB->real_escape_string($this->request->getPost('width', '')),
                $this->app->DB->real_escape_string($this->request->getPost('alignment', 'center')),
                $this->request->post->getBool('sum', false),
                $this->request->post->getInt('id', 0),
                $this->request->post->getInt('sequence', 0),
                $this->app->DB->real_escape_string($this->request->getPost('sorting', 'numeric')),
                $formatType,
                $formatStatement
            );
            $newId = $this->service->saveColumn($column, $reportId);
            $newCol = $this->gateway->getColumnById($newId);
            $data = ['success' => true, 'column' => $newCol, 'id' => $newId];
            $response = new JsonResponse($data, 200);
            $response->send();
        } catch (ColumnFormatException $e) {
            $response = new Response('Format expression invalid.', Response::HTTP_NOT_ACCEPTABLE);
            $response->send();
        } catch (Exception $e) {
            $response = new Response($e->getMessage(), 500);
            $response->send();
        }

        $this->app->ExitXentral();
    }

    /**
     * @return void
     */
    public function ajaxDeleteColumn()
    {
        $reportId = $this->request->post->getInt('report_id', 0);
        $id = $this->request->post->getInt('id', 0);
        $deleteAll = $this->request->post->getBool('delete_all', false);

        if ($id < 1 && $deleteAll === false) {
            $response = new Response('Column not found.', Response::HTTP_NOT_FOUND);
            $response->send();
            $this->app->ExitXentral();
        }

        if ($this->gateway->isReportReadonly($reportId)) {
            $response = new Response('Report is readonly', Response::HTTP_FORBIDDEN);
            $response->send();
            $this->app->ExitXentral();
        }

        try {
            if ($deleteAll === true) {
                $deleted = $this->service->deleteAllColumnsByReportId($reportId);
                $response = new JsonResponse(['success' => $deleted > 0], Response::HTTP_OK);
            } else {
                $this->service->deleteColumnById($id);
                $response = new JsonResponse(['success' => true], Response::HTTP_OK);
            }
        } catch (Exception $e) {
            $response = new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response->send();
        $this->app->ExitXentral();
    }

    /**
     * @return void
     */
    public function ajaxTryQuery()
    {
        $statement = $this->request->post->get('statement', '');
        $parameters = $this->request->post->get('parameters', []);
        if (empty($statement)) {
            $data = ['success' => true, 'messagetype' => 'info', 'message' => ''];
        } else {
            try {
                $data = $this->service->testSqlStatement($statement, $parameters);
                $data['success'] = true;
            } catch (Exception $e) {
                $data = ['success' => true, 'messagetype' => 'error', 'message' => $e->getMessage()];
            }
        }
        $response = new JsonResponse($data, Response::HTTP_OK);
        $response->send();
        $this->app->ExitXentral();
    }

    /**
     * @return void
     */
    public function ajaxReportUser()
    {
        $cmd = $this->request->getGet('cmd', '');
        $postData = [
            'id' => $this->request->post->getInt('id', 0),
            'report_id' => $this->request->post->getInt('report_id', 0),
            'user_id' => $this->request->post->getInt('user_id', 0),
            'chart_enabled' => $this->request->post->getInt('chart_enabled', 0),
            'file_enabled' => $this->request->post->getInt('file_enabled', 0),
            'menu_enabled' => $this->request->post->getInt('menu_enabled', 0),
            'tab_enabled' => $this->request->post->getInt('tab_enabled', 0),
            'name' => $this->request->getPost('name', ''),
        ];

        if (isset($postData['name']) && !empty($postData['name'] && empty($postData['user_id']))) {
            $nameparts = explode(':', $postData['name']);
            $postData['user_id'] = (int)$nameparts[0];
            $postData['name'] = trim($nameparts[1]);
        }

        switch ($cmd) {
            case 'ajaxGetShareUser':
                $data = $this->gateway->findSharedUserById($postData['id']);
                $response = new JsonResponse($data, Response::HTTP_OK);
                $response->send();

                break;

            case 'ajaxSaveShareUser':
                if ($postData['report_id'] < 1 || $postData['user_id'] < 1) {
                    $response = new Response('Failed to share to user.', Response::HTTP_NOT_ACCEPTABLE);
                    $response->send();
                    $this->app->ExitXentral();
                }

                if (
                    $postData['id'] < 1
                    && $this->gateway->isSharedUserOfReport($postData['user_id'], $postData['report_id'])
                ) {
                    $response = new Response(
                        'Report already shared to user.',
                        Response::HTTP_BAD_REQUEST
                    );
                    $response->send();
                    $this->app->ExitXentral();
                }

                try {
                    $newId = $this->service->saveReportUserArray($postData);
                    if ($newId > 0) {
                        $response = new JsonResponse(['id' => $newId], Response::HTTP_OK);
                    } else {
                        $response = new Response(
                            'Failed to share to user.',
                            Response::HTTP_INTERNAL_SERVER_ERROR
                        );
                    }
                } catch (Exception $e) {
                    $response = new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                $response->send();

                break;

            case 'ajaxDeleteShareUser':
                $removed = $this->service->tryDeleteUserShare($postData['id']);
                $response = new JsonResponse(['success' => $removed], Response::HTTP_OK);
                $response->send();

                break;

            default:
                $response = new Response('Unrecognized Command.', Response::HTTP_BAD_REQUEST);
                $response->send();

                break;
        }

        $this->app->ExitXentral();
    }

    /**
     * @return bool
     */
    public function isUpdateMessageNeedet()
    {
        $sqlFindNewReports = 'SELECT COUNT(r.id) as `count` FROM `report` as `r` WHERE r.readonly = 0';
        $newReports = (int)$this->app->DB->Select($sqlFindNewReports);
        $sqlFindOldReports = 'SELECT COUNT(b.id) as `count` FROM `berichte` as `b`';
        $oldReports = (int)$this->app->DB->Select($sqlFindOldReports);
        $hasNewReports = ($newReports !== 0);
        $hasOldReports = ($oldReports !== 0);

        return $hasOldReports && !$hasNewReports;
    }

    protected function renderParameterInputFields($reportId, $paramValues = [])
    {
        $report = $this->gateway->getReportById($reportId);
        if ($report === null) {
            return;
        }
        $parameters = $report->getParameters();
        if ($parameters === null) {
            return;
        }
        $hasEditableParams = false;
        foreach ($parameters as $param) {
            if ($param->isEditable()) {
                $hasEditableParams = true;
                break;
            }
        }

        $userId = (int)$this->app->User->GetID();
        $doRenderCsvExport = $this->gateway->userCanDownloadCsv($report->getId(), $userId);
        $doRenderPdfExport = $this->gateway->userCanDownloadPdf($report->getId(), $userId);
        if (!$doRenderCsvExport && !$doRenderPdfExport && !$hasEditableParams) {
            return;
        }

        $paramsHtml = '';
        if ($hasEditableParams) {
            foreach ($parameters as $parameter) {
                if (isset($paramValues[$parameter->getVarname()]) && $paramValues[$parameter->getVarname()] !== '') {
                    $parameter->setTemporaryValue($paramValues[$parameter->getVarname()]);
                }
                $paramsHtml .= $this->getParameterHtml($parameter);
            }

            $paramsHtml = sprintf(
                '<fieldset id="view-input-parameters">
                     <legend>{|Parameter|}</legend>   
                    <form method="post" action="">
                        <input type="hidden" name="id" value="[REPORT_ID]">
                          %s
                          <div class="form-group"></div>
                        <input class="button" type="submit" id="report-view-input-param-submit"
                         name="submit_parameters" value="{|Parameter festlegen|}">
                    </form>
                </fieldset>',
                $paramsHtml
            );
        }

        $exportHtml = '';
        if ($doRenderPdfExport || $doRenderCsvExport) {
            $paramKeysForLink = array_diff(array_keys($paramValues), ['module', 'action', 'id']);
            $paramsForLink = [];
            foreach($paramKeysForLink as $paramKey) {
              $paramsForLink[$paramKey] = $paramValues[$paramKey];
            }
            if ($doRenderCsvExport) {
                $exportHtml .= sprintf(
                    '<div><a class="button button-block active" 
                    href="?module=report&amp;action=download&amp;format=csv&amp;id=%d&%s"
                    >{|Export CSV|}</a></div>', $reportId, http_build_query($paramsForLink));
            }
            if ($doRenderPdfExport) {
                $exportHtml .= sprintf(
                    '<div><a class="button button-block active" 
                    href="?module=report&amp;action=download&amp;format=pdf&amp;id=%d&%s"
                    >{|Export PDF|}</a></div>', $reportId, http_build_query($paramsForLink));
            }
            $exportHtml = sprintf(
                '<fieldset id="view-input-parameters"><legend>{|Download|}</legend>%s</fieldset>',
                $exportHtml
            );
        }

        $html = sprintf(
            '<div class="col-xs-12 col-sm-2 col-sm-height">
                <div class="inside inside-full-height">
                    %s
                    %s
                </div>
            </div>',
            $paramsHtml, $exportHtml
        );

        $this->app->Tpl->Set('INPUT_PARAMETERS', $html);
    }

    protected function getParameterHtml(ReportParameter $parameter)
    {
        if (!$parameter->isEditable()) {
            return '';
        }

        $name = $parameter->getVarname();
        $display = $parameter->getDisplayname();
        $description = $parameter->getDescription();
        $value = $parameter->getValue();
        if (empty($value)) {
            $value = '';
        }
        $id = sprintf('report-view-input-param-%s', $parameter->getId());
        $element = '';

        switch ($parameter->getControlType()) {

            case 'combobox':
                $element = sprintf(
                    '<label for="%1$s">{|%2$s|}:</label>
                    <select class="live-filter-select" id="%1$s" name="%3$s">',
                    $id, $display, $name
                );
                $options = $parameter->getOptions();
                foreach ($options as $option) {
                    $selected = '';
                    /** @var ReportParameterOptionValue $option */
                    if ($option->getValue() == $value) {
                        $selected = 'selected';
                    }
                    $element .= sprintf(
                        '<option value="%2$s" %3$s>{|%1$s|}</option>',
                        $option->getDescription(), $option->getValue(), $selected
                    );
                }
                $element .= '</select>';

                break;

            default:
                $element = sprintf(
                    '<label for="%1$s">{|%2$s|}:</label>
                    <input class="live-filter-input" type="text" id="%1$s" name="%3$s" value="%4$s"/>',
                    $id, $display, $name, $value
                );
        }
        switch ($parameter->getControlType()) {

            case 'date':
                $this->app->Tpl->Add(
                    'JQUERY',
                    '$( "#' . $id . '" ).datepicker({
                    dateFormat: \'yy-mm-dd\',
                    dayNamesMin: [\'SO\', \'MO\', \'DI\', \'MI\', \'DO\', \'FR\', \'SA\'],
                    firstDay:1,
                    showWeek: true, monthNames: [
                        \'Januar\', \'Februar\', \'März\', \'April\', \'Mai\', \'Juni\', 
                        \'Juli\', \'August\', \'September\', \'Oktober\',  \'November\', \'Dezember\'
                    ],
                    });'
                );
                break;

            case 'autocomplete_project':
                $this->app->YUI->AutoComplete($id, 'projektname');
                break;

            case 'autocomplete_group':
                $this->app->YUI->AutoComplete($id, 'gruppe');
                break;

            case 'autocomplete_address':
                $this->app->YUI->AutoComplete($id, 'adresse');
                break;

            case 'autocomplete_article':
                $this->app->YUI->AutoComplete($id, 'artikelnummer');
                break;

            default:
        }
        $element = sprintf(
            '<div class="form-group">%s</div>',
            $element
        );

        return $element;
    }

    protected function drawTooltipHtml($content)
    {
        if ($content === '') {
            return '';
        }

        return '
        <a href="#" class="tooltip-inline">
			<span class="icon icon-tooltip"></span>
			<span class="tooltip" role="tooltip">
                <span class="tooltip-content">' .$content. '</span>
            </span>
		</a>'; //                <span class="tooltip-title">Beschreibung:</span>
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function getReportFormData(Request $request)
    {
        if ($request->getPost('submit', null) === null) {
            return [];
        }
        $name = $this->app->DB->real_escape_string(trim($request->getPost('name', '')));
        $description = strip_tags(
            $this->app->DB->real_escape_string(trim($request->getPost('description', '')))
        );
        $description = mb_str_replace("\\r\\n", "\r\n", $description);
        $project = $this->app->DB->real_escape_string($request->getPost('project', ''));
        $query = trim($request->getPost('sql_query', ''));
        $id = $request->post->getInt('reportId', 0);
        $projectId = $this->getProjectIdByName($project);
        $remarks = trim($request->getPost('remark', ''));
        $csvDelimiter = $request->getPost('csv_delimiter', '');
        $csvEnclosure = trim($request->getPost('csv_enclosure', ''));
        $category = $this->app->DB->real_escape_string($request->getPost('category', ''));

        return [
            'name'        => $name,
            'description' => $description,
            'project'     => $projectId,
            'sql_query'   => $query,
            'id'          => $id,
            'remark'      => $remarks,
            'csv_delimiter' => $csvDelimiter,
            'csv_enclosure' => $csvEnclosure,
            'category'    => $category,
        ];
    }

    /**
     * @param array $formData
     *
     * @return array
     */
    protected function getFormErrorMessage($formData)
    {
        if (empty($formData)) {
            return [];
        }
        $errors = [];
        if (empty($formData['name'])) {
            $errors['MSGNAME'] = 'Pflichtfeld';
        }

        return $errors;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function getTransferFormData(Request $request)
    {
        if (
            $request->getPost('submit', null) === null
            && $request->getPost('create_url', null) === null
        ) {
            return [];
        }

        $data =  [
            'id' => $request->post->getInt('id', 0),
            'report_id' => $request->get->getInt('id', 0),
            'ftp_active' => $request->getPost('ftp_active', 0),
            'ftp_passive' => $request->getPost('ftp_passive', 0),
            'ftp_type' => $request->getPost('ftp_type', ''),
            'ftp_host' => $request->getPost('ftp_host', ''),
            'ftp_port' => $request->getPost('ftp_port', ''),
            'ftp_user' => $request->getPost('ftp_user', ''),
            'ftp_password' => $request->getPost('ftp_password', ''),
            'ftp_interval_mode' => $request->getPost('ftp_interval_mode', ''),
            'ftp_interval_value' => $request->post->getInt('ftp_interval_value', 0),
            'ftp_daytime' => $request->getPost('ftp_daytime', ''),
            'ftp_format' => $request->getPost('ftp_format', ''),
            'ftp_filename' => $request->getPost('ftp_filename', ''),
            'email_active' => $request->getPost('email_active', 0),
            'email_recipient' => $request->getPost('email_recipient', ''),
            'email_subject' => $request->getPost('email_subject', ''),
            'email_interval_mode' => $request->getPost('email_interval_mode', ''),
            'email_interval_value' => $request->post->getInt('email_interval_value', 0),
            'email_daytime' => $request->getPost('email_daytime', ''),
            'email_format' => $request->getPost('email_format', ''),
            'email_filename' => $request->getPost('email_filename', ''),
            'url_format' => $request->getPost('url_format', ''),
            'url_begin' => $this->formatDateTime($request->getPost('url_begin', ''),'d.m.Y', 'Y-m-d'),
            'url_end' => $this->formatDateTime($request->getPost('url_end', ''),'d.m.Y', 'Y-m-d'),
            'url_address' => $request->getPost('url_address', ''),
            'api_active' => $request->getPost('api_active', 0),
            'api_account_id' => $this->getApiIdByName($request->getPost('api_account_name', '')),
            'api_format' => $request->getPost('api_format', ''),
        ];

        if ($data['ftp_active'] === 'on') {
            $data['ftp_active'] = 1;
        }
        if ($data['ftp_passive'] === 'on') {
            $data['ftp_passive'] = 1;
        }
        if ($data['email_active'] === 'on') {
            $data['email_active'] = 1;
        }
        if ($data['api_active'] === 'on') {
            $data['api_active'] = 1;
        }

        return $data;
    }

    /**
     * @param array          $form
     * @param TemplateParser $template
     */
    protected function renderForm($form, TemplateParser $template)
    {
        $template->Set('NAME', $form['name']);
        $template->Set('KURZUEBERSCHRIFT2', $form['name']);
        $template->Set('CATEGORY', $form['category']);
        $template->Set('DESCRIPTION', $form['description']);
        $projectName = $this->getProjectNameById($form['project']);
        $template->Set('PROJECT', $projectName);
        $template->Set('SQLQUERY', $form['sql_query']);
        $template->Set('REMARK', $form['remark']);
        $template->Set(
            'CSV_DELIMITER_OPTIONS',
            $this->renderOptions([
                ',' => ',',
                ';' => ';',
                ':' => ':',
                "\t" => "Tabulator",
                ' ' => 'Leerzeichen'
            ],
                $form['csv_delimiter'])
        );
        $enclosure = $form['csv_enclosure'];
        if ($enclosure === '"') {
            $enclosure = '&quot;';
        }
        $template->Set(
            'CSV_ENCLOSURE_OPTIONS',
            $this->renderOptions([
                '\'' => '\'',
                '&quot;' => '"',
                '' => 'keiner',
            ],
                $enclosure)
        );

        $template->Set('ID', $form['id']);
        if (isset($form['id']) && $form['id'] > 0) {
            $template->Set(
                'JSON_EXPORT_BUTTON',
                '<a class="button button-add active button-primary"
                        id="dowload-structure-file" 
						href="?module=report&action=download&format=json&id=[ID]">{|Definitionsdatei|}</a>'
            );
        }
    }

    /**
     * @param                $form
     * @param TemplateParser $template
     */
    protected function renderTransferForm($form, TemplateParser $template)
    {
        $report = $this->gateway->getReportById((int)$form['report_id']);
        if($report !== null) {
            $template->Set('KURZUEBERSCHRIFT2', $report->getName());
        }
        $form['url_begin'] = $this->formatDateTime($form['url_begin'],'Y-m-d', 'd.m.Y');
        $form['url_end'] = $this->formatDateTime($form['url_end'],'Y-m-d', 'd.m.Y');
        $form['ftp_daytime'] = $this->formatDateTime($form['ftp_daytime'],'H:i:s', 'H:i');
        $form['email_daytime'] = $this->formatDateTime($form['email_daytime'],'H:i:s', 'H:i');
        $form['api_account_name'] = $this->getApiNameById($form['api_account_id']);

        foreach ($form as $key => $value) {
            $template->Set(strtoupper($key), $value);
        }

        if ($form['ftp_active'] === 1) {
            $template->Set('FTP_ACTIVE_CHECKED', 'checked');
        }
        if ($form['ftp_passive'] === 1) {
            $template->Set('FTP_PASSIVE_CHECKED', 'checked');
        }
        if ($form['email_active'] === 1) {
            $template->Set('EMAIL_ACTIVE_CHECKED', 'checked');
        }
        if ($form['api_active'] === 1) {
            $template->Set('API_ACTIVE_CHECKED', 'checked');
        }

        $template->Set(sprintf('FTP_TYPE_%s_SELECTED', strtoupper($form['ftp_type'])), 'selected');
        $template->Set(sprintf('FTP_FORMAT_%s_SELECTED', strtoupper($form['ftp_format'])), 'selected');
        $template->Set(sprintf('FTP_INTERVAL_MODE_%s_SELECTED', strtoupper($form['ftp_interval_mode'])), 'selected');
        $template->Set(sprintf('EMAIL_FORMAT_%s_SELECTED', strtoupper($form['email_format'])), 'selected');
        $template->Set(sprintf('EMAIL_INTERVAL_MODE_%s_SELECTED', strtoupper($form['email_interval_mode'])), 'selected');
        $template->Set(sprintf('URL_FORMAT_%s_SELECTED', strtoupper($form['url_format'])), 'selected');
        $template->Set(sprintf('API_FORMAT_%s_SELECTED', strtoupper($form['api_format'])), 'selected');
    }

    /**
     * @param array          $columnForm
     * @param TemplateParser $template
     */
    protected function renderColumnView($columnForm, TemplateParser $template)
    {
        /** @var Template $subTemplate */
        $subTemplate = $this->app->Container->get('Template');
        $subTemplate->setDefaultNamespace('Modules/Report');
        $subTemplate->assign('columns', $columnForm);
        $table = $subTemplate->fetch('columns.tpl');
        $template->Set('COLUMNTABLE', $table);
    }

    /**
     * @param array          $parameterData
     * @param TemplateParser $template
     */
    protected function renderParameterView($parameterData, TemplateParser $template)
    {
        /** @var Template $subTemplate */
        $subTemplate = $this->app->Container->get('Template');
        $subTemplate->setDefaultNamespace('Modules/Report');
        $subTemplate->assign('parameters', $parameterData);
        $table = $subTemplate->fetch('parameter.tpl');
        $template->Set('PARAMTABLE', $table);
    }

    /**
     * @param array          $errors
     * @param TemplateParser $template
     *
     * @return void
     */
    protected function renderFormErrors($errors, TemplateParser $template)
    {
        if (!empty($errors)) {
            $this->renderErrorMessages(['Bitte alle Pflichtfelder ausfüllen!'], $template, 'FORMMESSAGE');
        }
        foreach ($errors as $error => $message) {
            $template->Set($error, $message);
        }
    }

    /**
     * @param array          $errors
     * @param TemplateParser $template
     * @param string         $varname
     */
    protected function renderErrorMessages($errors, TemplateParser $template, $varname = 'MESSAGE')
    {
        foreach ($errors as $error) {
            $template->Add($varname, sprintf('<div class="error">%s</div>', $error));
        }
    }

    /**
     * @param array $options
     * @param string $preselectedValue
     *
     * @return string
     */
    protected function renderOptions($options, $preselectedValue = '')
    {
        $optionsHtml = '';
        foreach ($options as $value => $display) {
            $selected = '';
            if ($value === $preselectedValue) {
                $selected = ' selected';
            }
            $optionsHtml .= sprintf('<option value="%s"%s>%s</option>', $value, $selected, $display);
        }

        return $optionsHtml;
    }

    /**
     * @param int $transferId
     * @param int $reportId
     *
     * @return bool
     */
    protected function createUrl($transferId, $reportId)
    {
        $token = StringUtil::random(256, true);

        $url = sprintf(
            '%s/index.php?module=report&action=export&id=%s&cmd=%s',
            $this->request->getBaseUrl(),
            $reportId,
            $token
        );

        /** @var Database $db */
        $db = $this->app->Container->get('Database');
        $sql = 'UPDATE report_transfer SET url_address=:url, url_token=:token WHERE id=:transferId';
        $values = ['url' => $url, 'token' => $token, 'transferId' => $transferId];
        $affected = $db->fetchAffected($sql, $values);

        return $affected > 0;
    }

    /**
     * @param string $dateTime
     * @param string $formatFrom
     * @param string $formatTo
     *
     * @return string
     */
    private function formatDateTime($dateTime, $formatFrom, $formatTo)
    {
        $dateObject = DateTime::createFromFormat($formatFrom, $dateTime);

        if ($dateObject === false) {
            return '';
        }
        $dateString = $dateObject->format($formatTo);
        if ($dateString === false) {
            return '';
        }

        return $dateString;
    }

    /**
     * @param string $apiName
     *
     * @return int
     */
    private function getApiIdByName($apiName)
    {
        if (empty($apiName)) {
            return 0;
        }
        $nameParts = explode(' ', $apiName);
        if (count($nameParts) < 2) {
            return 0;
        }
        $id = $nameParts[0];
        $sql = sprintf("SELECT p.id FROM `api_account` AS `p` WHERE p.id = '%s' LIMIT 1", $id);
        $idValue = $this->app->DB->Select($sql);

        return (int)$idValue;
    }

    /**
     * @param int $apiId
     *
     * @return string
     */
    private function getApiNameById($apiId)
    {
        if ($apiId < 1) {
            return '';
        }
        $sql = sprintf(
            'SELECT p.bezeichnung
                    FROM `api_account` AS `p`
                    WHERE p.id = %s LIMIT 1',
            (int)$apiId
        );
        $name = $this->app->DB->Select($sql);

        return sprintf('%s %s', $apiId, $name);
    }

    /**
     * @param string $projectName
     *
     * @return int
     */
    private function getProjectIdByName($projectName)
    {
        if (empty($projectName)) {
            return 0;
        }
        $nameParts = explode(' ', $projectName);
        if (count($nameParts) < 2) {
            return 0;
        }
        $shortName = $nameParts[0];
        $sql = sprintf("SELECT p.id FROM projekt AS p WHERE p.abkuerzung = '%s' LIMIT 1", $shortName);
        $idValue = $this->app->DB->Select($sql);

        return (int)$idValue;
    }

    /**
     * @param int $projectId
     *
     * @return string
     */
    private function getProjectNameById($projectId)
    {
        if ($projectId < 1) {
            return '';
        }
        $sql = sprintf(
            'SELECT p.abkuerzung, p.name
                    FROM projekt AS p 
                    WHERE p.id = %s LIMIT 1',
            (int)$projectId
        );
        $row = $this->app->DB->SelectRow($sql);

        return sprintf('%s %s', $row['abkuerzung'], $row['name']);
    }

    /**
     * @param string $url
     */
    private function redirectTo($url)
    {
        $redirect = RedirectResponse::createFromUrl($url);
        $redirect->send();
    }

    /**
     * @return void
     */
    private function createMenu()
    {
        $id = (int)$this->app->Secure->GetGET('id');
        $action = $this->request->getGet('action', 'list');
        $idParam = sprintf('&id=%s', $id);

        $this->app->erp->MenuEintrag(
            'index.php?module=report&action=list',
            'Zur&uuml;ck zur &Uuml;bersicht'
        );

        if ($action === 'list') {
            $this->app->erp->MenuEintrag('index.php?module=report&action=list', '&Uuml;bersicht');
        } else {
            $this->app->erp->MenuEintrag(
                sprintf('index.php?module=report&action=view%s', $idParam),
                'Ansicht'
            );
            $this->app->erp->MenuEintrag(
                sprintf('index.php?module=report&action=edit%s', $idParam),
                'Details'
            );
            $this->app->erp->MenuEintrag(
                sprintf('index.php?module=report&action=share%s', $idParam),
                'Freigaben'
            );
            $this->app->erp->MenuEintrag(
                sprintf('index.php?module=report&action=transfer%s', $idParam),
                'Übertragung'
            );
        }
    }

    /**
     * @return void
     */
    private function installJsonReports()
    {
        $importDir = dirname(__DIR__, 2) . '/classes/Modules/Report/files';
        if (!is_dir($importDir)) {
            $this->app->erp->LogFile('Importverzeichnis kann nicht gefunden werden.', $importDir);

            return;
        }
        $allFiles =scandir($importDir);
        $importPaths = [];
        foreach ($allFiles  as $filename) {
            if (StringUtil::endsWith($filename, '.json')) {
                $importPaths[] = sprintf('%s/%s', $importDir, $filename);
            }
        }
        if (count($importPaths) === 0) {
            $this->app->erp->LogFile('No files available for import.', $importDir);

            return;
        }
        if (!$this->app->Container->has('ReportJsonImportService')) {
            $this->app->erp->LogFile('Service ReportJsonImportService not available.');

            return;
        }

        $importedReportNames = [];

        /** @var ReportJsonImportService $importer */
        $importer = $this->app->Container->get('ReportJsonImportService');
        foreach ($importPaths as $filePath) {
            try {
                $content = file_get_contents($filePath);
                $data = json_decode($content, true);
                $errors = $importer->findJsonStructureErrors($data);
                if (count($errors) > 0) {
                    $this->app->erp->Logfile(
                        sprintf(
                        'Json parse error in File %s.', $filePath),
                        implode("\n", $errors)
                    );
                    throw new JsonParseException(sprintf('Json parse error in File %s', $filePath));
                }
                $data['readonly'] = true;
                $importer->importReport($data);
                $importedReportNames[] = $data['name'];
            } catch (Exception $e) {
                $this->app->erp->LogFile(sprintf('Import of json file failed %s', $filePath),$e);
            }
        }

        $sqlFindinstalled = 'SELECT r.id, r.name
                            FROM `report` AS `r` 
                            WHERE r.readonly = 1';
        $installedreports = $this->app->DB->SelectPairs($sqlFindinstalled);

        /** @var ReportService $service */
        $service = $this->app->Container->get('ReportService');
        foreach ($installedreports as $id => $name) {
            if (!in_array($name, $importedReportNames, true)) {
                $service->deleteReportById($id);
            }
        }
    }
}
