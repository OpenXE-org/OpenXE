<?php

namespace Xentral\Modules\DocumentScanner\DataTable;

use Aura\SqlQuery\Exception;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Widgets\DataTable\Column\Column;
use Xentral\Widgets\DataTable\Column\ColumnCollection;
use Xentral\Widgets\DataTable\Column\ColumnFormatter;
use Xentral\Widgets\DataTable\Feature\ColumnFilterFeature;
use Xentral\Widgets\DataTable\Feature\FeatureCollection;
use Xentral\Widgets\DataTable\Filter\CustomFilter;
use Xentral\Widgets\DataTable\Filter\FilterCollection;
use Xentral\Widgets\DataTable\Options\DataTableOptions;
use Xentral\Widgets\DataTable\Request\DataTableRequest;
use Xentral\Widgets\DataTable\Type\AbstractDataTableType;

final class DocScanFilesDataTable extends AbstractDataTableType
{
    /**
     * @param ColumnCollection $columns
     *
     * @return void
     */
    public function configureColumns(ColumnCollection $columns)
    {
        $preview = Column::visible('preview', 'Vorschau', 'center', '10%');
        $preview->setFormatter(ColumnFormatter::template(
            '<span style="width:100px;text-align:center;display:block;">' .
            '<a href="index.php?module=dateien&action=send&id={ID}">' .
            '<img src="index.php?module=ajax&action=thumbnail&cmd=docscan&id={ID}" alt="{DATEINAME}"'.
            ' style="border:0;max-width:100px;max-height:100px;">' .
            '</a>' .
            '</span>'
        ));

        $title = Column::searchable('titel', 'Titel', 'left', '30%');
        $title->setFormatter(function ($value, $row) {
            return !empty($value)
                ? sprintf('%s<br><i style="color:#999;">%s</i>', $value, $row['dateiname'])
                : $row['dateiname'];
        });

        $filesize = Column::sortable('filesize', 'Dateigröße', 'right', '10%');
        $filesize->setFormatter(ColumnFormatter::bytes());

        $menu = Column::fixed('menu', 'Menü', 'center', '1%');
        $menu->setFormatter(ColumnFormatter::template(
            '<table class="datatable-menu docscan-menu" align="center" border="0" cellpadding="0" cellspacing="0"><tr>' .
            '<td><a href="#" class="docscan-add-button" data-file="{FILE_ID}" data-type="{DOC_TYPE}" title="Datei zuweisen">' .
            '<img src="themes/new/images/add.png" alt="Datei zuweisen" border="0" align="center">' .
            '</a></td>' .
            '<td><a href="#" class="docscan-delete-button" data-file="{FILE_ID}" data-type="{DOC_TYPE}" title="Datei löschen">' .
            '<img src="themes/new/images/delete.svg" alt="Datei löschen" border="0" align="center">' .
            '</a></td>' .
            '</tr></table>'
        ));

        $date = Column::sortable('datum', 'Datum');
        $date->setFormatter(ColumnFormatter::date('d.m.Y'));

        //$columns->add(Column::hidden('id', 'ID'));
        $columns->add($preview);
        $columns->add($title);
        $columns->add(Column::searchable('subjekt', 'Stichwort'));
        $columns->add(Column::searchable('objekte', 'Zuordnung'));
        $columns->add($filesize);
        $columns->add(Column::searchable('ersteller', 'Ersteller'));
        $columns->add(Column::searchable('bemerkung', 'Bemerkung'));
        $columns->add($date);
        $columns->add($menu);
    }

    /**
     * @param SelectQuery $query
     *
     * @throws Exception
     *
     * @return void
     */
    public function configureQuery(SelectQuery $query)
    {
        $query
            ->cols([
                'd.id',
                'd.titel',
                'v.dateiname',
                'v.size AS filesize',
                'v.ersteller',
                'v.bemerkung',
                'v.datum',
                's.subjekt',
                'zuweisungen.objekte',
                'd.id AS file_id',
                'sb.kategorie AS doc_type',
            ])
            ->from('datei AS d')
            ->leftJoin('datei_stichwoerter AS s', 'd.id = s.datei')
            ->innerJoin('docscan AS sb', 'd.id = sb.datei AND s.parameter = sb.id')
            ->joinSubSelect(
                'LEFT',
                'SELECT ds.datei FROM datei_stichwoerter AS ds WHERE ds.objekt NOT LIKE \'DocScan\' GROUP BY ds.datei',
                'notdocscan',
                'notdocscan.datei = d.id'
            )
            ->joinSubSelect(
                'LEFT',
                'SELECT ds.datei, GROUP_CONCAT(ds.objekt SEPARATOR \', \') AS objekte
                 FROM datei_stichwoerter AS ds 
                 WHERE ds.objekt NOT LIKE \'DocScan\'
                 GROUP BY ds.datei',
                'zuweisungen',
                'zuweisungen.datei = d.id'
            )
            ->joinSubSelect(
                'LEFT',
                'SELECT dvi1.datei, max(dvi1.version) AS version FROM datei_version AS dvi1 GROUP BY dvi1.datei',
                'v2',
                'v2.datei = d.id'
            )
            ->leftJoin('datei_version AS v', 'v.datei = v2.datei AND v.version = v2.version')
            ->where('s.objekt LIKE ?', 'DocScan')
            ->where('d.geloescht = 0');
    }

    /**
     * @param DataTableOptions $options
     *
     * @return void
     */
    public function configureOptions(DataTableOptions $options)
    {
        $options->setDefaultSorting(['datum' => 'DESC']);
    }

    /**
     * @param FeatureCollection $features
     *
     * @return void
     */
    public function configureFeatures(FeatureCollection $features)
    {
        $features->add(new ColumnFilterFeature());

        parent::configureFeatures($features);
    }

    /**
     * @param FilterCollection $filters
     *
     * @return void
     */
    public function configureFilters(FilterCollection $filters)
    {
        $closure = function (SelectQuery $query, DataTableRequest $request) {
            $filter = $request->getParams()->getFilterValues();
            if ($filter['show-all'] !== 'true') {
                $query->where('notdocscan.datei IS NULL');
            }
        };
        $filters->add(new CustomFilter($closure));
    }
}
