<?php

namespace Xentral\Widgets\DataTable\Service;

use Closure;
use Exception;
use Xentral\Components\Database\Database;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Components\Exporter\Csv\CsvConfig;
use Xentral\Components\Exporter\Csv\CsvWriter;
use Xentral\Components\Exporter\Exception\InvalidResourceException;
use Xentral\Widgets\DataTable\Column\Column;
use Xentral\Widgets\DataTable\DataTableBuildConfig;
use Xentral\Widgets\DataTable\DataTableInterface;
use Xentral\Widgets\DataTable\Exception\InvalidArgumentException;
use Xentral\Widgets\DataTable\Feature\DebugFeature;
use Xentral\Widgets\DataTable\Feature\RowClassesFeature;
use Xentral\Widgets\DataTable\Filter\FilterInterface;
use Xentral\Widgets\DataTable\Request\DataTableRequest;
use Xentral\Widgets\DataTable\Result\DataTableDataResult;

final class DataTableFetcher
{
    /** @var Database $db */
    private $db;

    /** @var DataTableRequest $request */
    private $request;

    /**
     * @param Database         $db
     * @param DataTableRequest $request
     */
    public function __construct(Database $db, DataTableRequest $request)
    {
        $this->db = $db;
        $this->request = $request;
    }

    /**
     * @param DataTableBuildConfig $buildConfig
     *
     * @return bool
     */
    public function canFetchData(DataTableBuildConfig $buildConfig)
    {
        if (!$this->request->isAjax()) {
            return false;
        }
        if ($this->request->getMethod() !== $buildConfig->getAjaxMethod()) {
            return false;
        }

        $tableNameDefined = $buildConfig->getTableName();
        $tableNameRequested = $this->request->getParams()->getTableName();

        return $tableNameDefined === $tableNameRequested;
    }

    /**
     * @param DataTableBuildConfig $buildConfig
     *
     * @return bool
     */
    public function canExportData(DataTableBuildConfig $buildConfig)
    {
        $exportParams = (array)$this->request->getParams()->getExportValues();
        if (empty($exportParams['format']) || empty($exportParams['result'])) {
            return false;
        }
        if ($this->request->isAjax()) {
            return false;
        }

        $tableNameDefined = $buildConfig->getTableName();
        $tableNameRequested = $this->request->getParams()->getTableName();

        return $tableNameDefined === $tableNameRequested;
    }

    /**
     * @param DataTableInterface $table
     *
     * @return DataTableDataResult
     */
    public function fetchData(DataTableInterface $table)
    {
        $startParam = $this->request->getParams()->getStart();
        $lengthParam = $this->request->getParams()->getLength();

        try {

            $debugging = false;
            if ($table->getFeatures()->has(DebugFeature::class)) {
                /** @var DebugFeature $debugFeature */
                $debugFeature = $table->getFeatures()->get(DebugFeature::class);
                $debugging = $debugFeature->isEnabled();
            }

            if ($debugging === true) {
                $debugData = ['profiler' => ['start' => microtime(true)]];
            }

            $baseQuery = $table->getBaseQuery();
            $cols = $baseQuery->getCols();

            // Set up query for total record count
            $recordsTotalQuery = clone $baseQuery;
            $recordsTotalQuery
                ->resetCols()
                ->cols([sprintf('COUNT(%s) AS num', $cols[0])]);

            // Apply filters and searches
            $this->applyFilters($table);
            $this->applyColumnSearch($table, $baseQuery);
            $this->applyGlobalSearch($table, $baseQuery);

            // Set up query for data + limit result set
            $dataQuery = clone $baseQuery;
            $dataQuery->offset($startParam);
            $dataQuery->limit($lengthParam);
            if ($startParam === -1 || $lengthParam === -1) {
                $dataQuery->offset(0);
                $dataQuery->limit(0);
            }

            // Apply ORDER BY + LIMIT
            $sortingValues = $this->prepareSortingValue($table);
            $this->applySorting($dataQuery, $sortingValues);
            $this->applyPaging($dataQuery, $startParam, $lengthParam);

            // Set up query for filtered record count
            // (= Record count with applied filters and searches)
            $recordsFilteredQuery = clone $baseQuery;
            $recordsFilteredQuery->resetCols()->cols([sprintf('COUNT(%s) AS num', $cols[0])]);

            // Ergebnisanzahl; mit Filter
            $recordsFiltered = $this->db->fetchValue(
                $recordsFilteredQuery->getStatement(),
                $recordsFilteredQuery->getBindValues()
            );

            // Ergebnisanzahl; ohne Filter
            $recordsTotal = $this->db->fetchValue(
                $recordsTotalQuery->getStatement(),
                $recordsTotalQuery->getBindValues()
            );

            // Fetch data; displayed result
            $data = $this->db->fetchAll(
                $dataQuery->getStatement(),
                $dataQuery->getBindValues()
            );

            // Column-Formatter anwenden
            $columnFormatters = $table->getColumns()->getFormatters();
            $this->applyColumnFormatters($data, $columnFormatters);

            // Row-Formatter anwenden
            // @todo In RowClassesFeature auslagern
            if ($table->getFeatures()->has(RowClassesFeature::class)) {
                /** @var RowClassesFeature $rowStyling */
                $rowStyling = $table->getFeatures()->get(RowClassesFeature::class);
                if ($rowStyling->hasCustomFormatter()) {
                    $rowFormatter = $rowStyling->getCustomFormatter();
                    foreach ($data as &$rowValues) {
                        $rowClasses = $rowStyling->getClassesString();
                        foreach ($rowFormatter as $closure) {
                            $rowClasses .= $closure($rowValues);
                        }
                        $rowValues['DT_RowClass'] = $rowClasses;
                    }
                    unset($rowValues);
                }
            }

            // ID-Attribut für jede Zeile setzen
            $tableName = $table->getConfig()->getTableName();
            $this->appendRowIdAttribute($data, $tableName);

            // Result-Objekt bauen
            $result = new DataTableDataResult(
                (int)$this->request->getParams()->getDraw(),
                (int)$recordsTotal,
                (int)$recordsFiltered,
                (array)$data
            );

        } catch (Exception $exception) {
            $result = new DataTableDataResult();
            $result->setErrorMessage(sprintf(
                'Unhandled exception: (%s) %s',
                get_class($exception),
                $exception->getMessage()
            ));
        }

        if ($debugging === true) {
            $debugData['profiler']['finish'] = microtime(true);
            $debugData['profiler']['duration_real'] = $debugData['profiler']['finish'] - $debugData['profiler']['start'];
            $debugData['profiler']['duration'] = sprintf('%.6f', $debugData['profiler']['duration_real']) . ' seconds';

            if (isset($dataQuery)) {
                $debugData['query']['statement'] = $dataQuery->getStatement();
                $debugData['query']['bindings'] = var_export($dataQuery->getBindValues(), true);
            }
            $result->setDebugInfo($debugData);
        }

        return $result;
    }

    /**
     * @param DataTableInterface $table
     *
     * @return string Path to export file
     */
    public function exportData(DataTableInterface $table)
    {
        $startParam = (int)$this->request->getParams()->getStart();
        $lengthParam = (int)$this->request->getParams()->getLength();
        $exportParams = (array)$this->request->getParams()->getExportValues();

        $exportFormat = !empty($exportParams['format']) ? $exportParams['format'] : 'csv';
        if ($exportFormat !== 'csv') {
            throw new InvalidArgumentException(sprintf(
                'Invalid export format "%s". Only "csv" is valid.', $exportFormat
            ));
        }

        $exportResult = !empty($exportParams['result']) ? $exportParams['result'] : 'page';
        if (!in_array($exportResult, ['all', 'page'], true)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid export result parameter value "%s". Valid values: %s',
                $exportResult,
                implode(', ', ['all', 'page'])
            ));
        }

        // Alle Ergebnisse exportieren
        if ($exportResult === 'all') {
            $startParam = -1;
            $lengthParam = -1;
        }

        $fileName = uniqid('export-' . $table->getConfig()->getTableName(), false) . '.csv';
        $filePath = sys_get_temp_dir() . '/' . $fileName;

        $csv = @fopen($filePath, 'x+b');
        if ($csv === false) {
            throw new InvalidResourceException(sprintf('Failed to open resource for file path "%s".', $filePath));
        }

        $writer = new CsvWriter($csv, new CsvConfig());

        $titles = [];
        $dbCols = [];
        foreach ($table->getColumns() as $column) {
            /** @var Column $column */
            if ($column->isExportable() && !empty($column->getDbColumn())) {
                $titles[] = $column->getTitle();
                $dbCols[] = $column->getDbColumn();
            }
        }
        $writer->writeLine($titles);

        $dataQuery = clone $table->getBaseQuery();
        $dataQuery->resetCols()->cols($dbCols);

        // Apply filters and searches
        $this->applyFilters($table);
        $this->applyColumnSearch($table, $dataQuery);
        $this->applyGlobalSearch($table, $dataQuery);

        // Apply ORDER BY
        $sortingValues = $this->prepareSortingValue($table);
        $this->applySorting($dataQuery, $sortingValues);

        $itemsPerStep = 2500;
        $currentOffset = 0;
        $hasResults = true;

        if ($exportResult === 'page') {
            $itemsPerStep = $lengthParam;
            $currentOffset = $startParam;
        }

        do {

            $dataQuery->offset($currentOffset);
            $dataQuery->limit($itemsPerStep);

            $data = $this->db->yieldAll(
                $dataQuery->getStatement(),
                $dataQuery->getBindValues()
            );

            if (!$data->valid()) {
                $hasResults = false;
                continue;
            }

            $writer->writeLines($data);

            $currentOffset += $itemsPerStep;

            // Nach einer Iteration aufhören, wenn nur eine Seite exportiert werden soll
            if ($exportResult === 'page') {
                $hasResults = false;
            }

        } while ($hasResults);

        fclose($csv);

        return $filePath;
    }

    /**
     * @param DataTableInterface $table
     *
     * @return void
     */
    private function applyFilters(DataTableInterface $table)
    {
        /** @var FilterInterface $filter */
        foreach ($table->getFilters() as $filter) {
            $filter->applyFilter($table, $this->request);
        }
    }

    /**
     * Suche über das allgemeine Suchfeld verarbeiten (oben rechts)
     *
     * @param DataTableInterface $table
     * @param SelectQuery        $query
     *
     * @return void
     */
    private function applyGlobalSearch(DataTableInterface $table, SelectQuery $query)
    {
        $searchValue = $this->getSearchParam();
        if (empty($searchValue)) {
            return;
        }

        $searchParts = explode(' ', $searchValue);
        $searchParts = array_filter($searchParts, 'trim');

        // Custom Search @todo Momentan ohne Funktion; Es gibt keine Möglichkeit zum Setzen der Einstellung
        // Beispiel-Setter:
        //$this->setCustomSearch(function (SelectQuery $query) {
        //    return $query
        //        ->cols(['artikel.id'])
        //        ->from('artikel')
        //        ->where('artikel.name_de LIKE :query')
        //        ->orWhere('artikel.name_en LIKE :query');
        //});
        //$customSearchClosure = $table->getCustomSearch();
        //if ($customSearchClosure !== null) {
        //    $matchColumn = $query->getCols()[0];
        //    $customSearchQuery = $customSearchClosure($this->db->select());
        //
        //    $query->joinSubSelect('inner', $customSearchQuery, 'matches', 'matches.id = ' . $matchColumn);
        //    $query->bindValue('query', '%' . $searchValue . '%');
        //
        //    return;
        //}

        // Normale Suche
        $searchableDbColumns = $table->getColumns()->getSearchableDbColumns();
        foreach ($searchParts as $searchWord) {
            $query->where(static function (SelectQuery $select) use ($searchableDbColumns, $searchWord) {
                foreach ($searchableDbColumns as $searchDbColumn) {
                    $select->orWhere(sprintf('%s LIKE ?', $searchDbColumn), '%' . $searchWord . '%');
                }
            });
        }
    }

    /**
     * @todo In ColumnFilterFeature auslagern
     *
     * @param DataTableInterface $table
     * @param SelectQuery        $query
     *
     * @return void
     */
    private function applyColumnSearch(DataTableInterface $table, SelectQuery $query)
    {
        //$columnFilter = $table->getFeatures()->get(ColumnFilterFeature::class);

        $params = (array)$this->request->getParams()->getColumnsValues();
        foreach ($params as $index => $param) {
            $searchValue = $param['search']['value'];
            if (empty($searchValue)) {
                continue;
            }

            // Spaltensuche wurde ausgefüllt
            $column = $table->getColumns()->getByName($param['name']);
            if ($column === null) {
                continue;
            }

            // Zahlenbereich-Suche
            if (strpos($searchValue, 'number_range:') === 0) {
                $searchPattern = str_replace([':null|', '|null'], '|', $searchValue);
                if ($searchPattern === 'number_range:|') {
                    continue; // Leere Suche
                }

                $searchPattern = str_replace('number_range:', '', $searchPattern);
                $searchParts = explode('|', $searchPattern);
                if (count($searchParts) !== 2) {
                    continue;
                }

                $valueFrom = str_replace(',', '.', $searchParts[0]);
                $valueTo = str_replace(',', '.', $searchParts[1]);
                if (is_numeric($valueFrom)) {
                    $query->where($column->getDbColumn() . ' >= ?', (float)$valueFrom);
                }
                if (is_numeric($valueTo)) {
                    $query->where($column->getDbColumn() . ' <= ?', (float)$valueTo);
                }
                continue;
            }

            // Normale Textsuche
            $query->where($column->getDbColumn() . ' LIKE ?', '%' . $searchValue . '%');
        }
    }

    /**
     * @see https://datatables.net/manual/server-side#Sent-parameters Parameters 'start' and 'length'
     *
     * @param SelectQuery $dataQuery
     * @param int         $startValue
     * @param int         $lengthValue
     *
     * @return void
     */
    private function applyPaging(SelectQuery $dataQuery, $startValue, $lengthValue)
    {
        $dataQuery->offset($startValue);
        $dataQuery->limit($lengthValue);
        if ($startValue === -1 || $lengthValue === -1) {
            $dataQuery->offset(0);
            $dataQuery->limit(0);
        }
    }

    /**
     * @param SelectQuery $query
     * @param array       $sortingValues
     *
     * @return void
     */
    private function applySorting(SelectQuery $query, array $sortingValues)
    {
        if (!empty($sortingValues)) {
            $query->resetOrderBy();
            foreach ($sortingValues as $sortColumn => $sortDirection) {
                $query->orderBy([sprintf('%s %s', $sortColumn, strtoupper($sortDirection))]);
            }
        }
    }

    /**
     * Column-Formatter anwenden
     *
     * @param array $data
     * @param array $formatters
     *
     * @return void
     */
    private function applyColumnFormatters(array &$data, array $formatters = [])
    {
        if (empty($formatters)) {
            return;
        }

        foreach ($formatters as $colName => $formatter) {
            if (!is_callable($formatter)) {
                continue;
            }
            foreach ($data as &$rowData) {
                $cellData = $rowData[$colName];
                $newValue = $this->callColumnFormatter($formatter, $cellData, $rowData);
                $rowData[$colName] = $newValue;
            }
            unset($rowData);
        }
    }

    /**
     * @param Closure $callback
     * @param string  $value
     * @param array   $rowValues
     *
     * @return string
     */
    private function callColumnFormatter(Closure $callback, $value, $rowValues)
    {
        return $callback($value, $rowValues);
    }

    /**
     * @param DataTableInterface $table
     *
     * @return array
     */
    private function prepareSortingValue(DataTableInterface $table)
    {
        $orderValue = $this->getOrderParam();

        $sorting = [];
        foreach ($orderValue as $orderItem) {
            $columnIndex = (int)$orderItem['column'];
            $column = $table->getColumns()->getByIndex($columnIndex);
            if ($column === null) {
                break;
            }
            $columnName = $column->getDbColumn();
            $sortDirection = in_array(strtolower($orderItem['dir']), ['asc', 'desc'], true)
                ? strtolower($orderItem['dir'])
                : null;

            if ($columnName !== null && $sortDirection !== null) {
                $sorting[$columnName] = strtoupper($sortDirection);
            }
        }

        return $sorting;
    }

    /**
     * ID-Attribut für jede Zeile setzen
     *
     * @param array  $data
     * @param string $tableName
     *
     * @return void
     */
    private function appendRowIdAttribute(&$data, $tableName)
    {
        // Row-ID hinzufügen
        foreach ($data as &$rowValues) {
            foreach ($rowValues as $key => &$value) {
                if ($key === 'id') {
                    $rowValues['DT_RowId'] = sprintf('%s_row_%s', $tableName, $value);
                }
            }
            unset($value);
        }
        unset($rowValues);
    }

    /**
     * @return string
     */
    private function getSearchParam()
    {
        return $this->request->getParams()->getSearchValues()['value'];
    }

    /**
     * @return array
     */
    private function getOrderParam()
    {
        return (array)$this->request->getParams()->getOrderValues();
    }
}
