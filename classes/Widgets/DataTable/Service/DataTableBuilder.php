<?php

namespace Xentral\Widgets\DataTable\Service;

use Xentral\Components\Database\Database;
use Xentral\Widgets\DataTable\Column\ColumnCollection;
use Xentral\Widgets\DataTable\DataTableBuildConfig;
use Xentral\Widgets\DataTable\Exception\BuildFailedException;
use Xentral\Widgets\DataTable\Options\DataTableOptions;
use Xentral\Widgets\DataTable\DataTableInterface;
use Xentral\Widgets\DataTable\Feature\DataTableFeatureInterface;
use Xentral\Widgets\DataTable\Feature\FeatureCollection;
use Xentral\Widgets\DataTable\Filter\FilterCollection;
use Xentral\Widgets\DataTable\PreparedDataTable;
use Xentral\Widgets\DataTable\Type\DataTableTypeInterface;

final class DataTableBuilder
{
    /** @var Database $database */
    private $database;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @param DataTableBuildConfig $config
     *
     * @throws BuildFailedException
     *
     * @return DataTableInterface
     */
    public function buildTable(DataTableBuildConfig $config)
    {
        if (empty(trim($config->getTableName()))) {
            throw new BuildFailedException('Build config is incomplete. Table name is empty.');
        }
        if (empty(trim($config->getAjaxUrl()))) {
            throw new BuildFailedException('Build config is incomplete. Property "ajaxUrl" is missing.');
        }
        if (!class_exists($config->getTableClass(), true)) {
            throw new BuildFailedException(sprintf('DataTable class "%s" not found', $config->getTableClass()));
        }
        $interfaces = class_implements($config->getTableClass(), true);
        if (!in_array(DataTableTypeInterface::class, $interfaces, true)) {
            throw new BuildFailedException(
                'Can not build data table. Class does not implement ' . DataTableTypeInterface::class
            );
        }

        /** @var DataTableTypeInterface $table */
        $className = $config->getTableClass();
        $table = new $className();

        // @todo getParent() verarbeiten

        $options = new DataTableOptions();
        $table->configureOptions($options);

        $columns = new ColumnCollection();
        $table->configureColumns($columns);

        $query = $this->database->select();
        $table->configureQuery($query);

        if ($query->hasOrderBy()) {
            throw new BuildFailedException(
                'Sorting in "configureQuery" will be overwritten. ' .
                'Use "setDefaultSorting" in "configureOptions" instead.'
            );
        }

        $features = new FeatureCollection();
        $table->configureFeatures($features);

        $filters = new FilterCollection();
        $table->configureFilters($filters);

        $preparedTable = new PreparedDataTable($config, $options, $query, $columns, $features, $filters);
        $this->prepareTable($preparedTable);

        return $preparedTable;
    }

    /**
     * @param DataTableInterface $table
     *
     * @return void
     */
    private function prepareTable(DataTableInterface $table)
    {
        $this->prepareColumns($table);
        $this->applyFeatures($table);
        $this->prepareSorting($table);
    }

    /**
     * @param DataTableInterface $table
     *
     * @return void
     */
    private function applyFeatures(DataTableInterface $table)
    {
        /** @var DataTableFeatureInterface $feature */
        foreach ($table->getFeatures() as $feature) {
            $feature->modifyTable($table);
        }
    }

    /**
     * @param DataTableInterface $table
     *
     * @return void
     */
    private function prepareColumns(DataTableInterface $table)
    {
        // Spalten aus dem SQL-Query holen
        $query = $table->getBaseQuery();
        $columnNames = $query->getCols();

        foreach ($columnNames as $alias => $fullColumnName) {
            // Spalten mit Spaltenaliasen zuerst behandeln (easy)
            $column = $table->getColumns()->getByName($alias);
            if ($column !== null) {
                $column->setDbColumn($fullColumnName);
                continue;
            }

            // Tabellenalias aus Spaltenname entfernen
            $shortColumnName = $this->extractNameFromColumn($fullColumnName);
            $column = $table->getColumns()->getByName($shortColumnName);
            if ($column !== null) {
                $column->setDbColumn($fullColumnName);
            }
        }
    }

    /**
     * @param DataTableInterface $table
     *
     * @return void
     */
    private function prepareSorting(DataTableInterface $table)
    {
        $columnNames = array_column($table->getColumns()->toArray(), 'data');
        $defaultSorting = $table->getOptions()->getDefaultSorting();
        $postSorting = $table->getOptions()->getPostSorting();
        $preSorting = $table->getOptions()->getPreSorting();

        $defaultSorting = $this->translateSortingValues($columnNames, $defaultSorting);
        $postSorting = $this->translateSortingValues($columnNames, $postSorting);
        $preSorting = $this->translateSortingValues($columnNames, $preSorting);

        /**
         * Sortierung, wenn nichts gesetzt ist; Benutzer-Sortierung überschreibt diesen Wert
         *
         * @see https://datatables.net/reference/option/order
         */
        if (empty($defaultSorting)) {
            $defaultSorting = [[0, 'asc']];
        }
        $table->getOptions()->setOption('order', $defaultSorting);

        /**
         * Vor- und Nach-Sortierung; Kann vom Benutzer nicht geändert werden
         *
         * @see https://datatables.net/reference/option/orderFixed
         */
        if (!empty($preSorting)) {
            $orderFixed['pre'] = $preSorting;
        }
        if (!empty($postSorting)) {
            $orderFixed['post'] = $postSorting;
        }
        if (!empty($orderFixed)) {
            $table->getOptions()->setOption('orderFixed', $orderFixed);
        } else {
            $table->getOptions()->removeOption('orderFixed');
        }
    }

    /**
     * @example ['lagerbestand' => 'DESC', 'bezeichnung' => 'ASC'] wird zu [[3, 'desc'], [1, 'asc']]
     *
     * @param array $columnNames
     * @param array $sortingValues
     *
     * @return array
     */
    private function translateSortingValues($columnNames, $sortingValues)
    {
        $result = [];

        foreach ($sortingValues as $columnName => $sortOrder) {
            $columnIndex = array_search($columnName, $columnNames, true);
            if ($columnIndex !== false) {
                $result[] = [$columnIndex, strtolower($sortOrder)];
            }
        }

        return $result;
    }

    /**
     * @param string $column
     *
     * @return string
     */
    private function extractNameFromColumn($column)
    {
        if ($pos = strrpos($column, '.')) {
            return substr($column, $pos + 1);
        }

        return $column;
    }
}
