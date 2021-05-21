<?php

namespace Xentral\Widgets\DataTable\Service;

use Xentral\Widgets\DataTable\DataTableBuildConfig;
use Xentral\Widgets\DataTable\DataTableInterface;
use Xentral\Widgets\DataTable\Result\DataTableDataResult;
use Xentral\Widgets\DataTable\Result\DataTableHtmlResult;

final class DataTableService
{
    /** @var DataTableBuilder $builder */
    private $builder;

    /** @var DataTableRenderer $renderer */
    private $renderer;

    /** @var DataTableFetcher $fetcher */
    private $fetcher;

    /**
     * @param DataTableBuilder  $builder
     * @param DataTableRenderer $renderer
     * @param DataTableFetcher  $fetcher
     */
    public function __construct(DataTableBuilder $builder, DataTableRenderer $renderer, DataTableFetcher $fetcher)
    {
        $this->builder = $builder;
        $this->renderer = $renderer;
        $this->fetcher = $fetcher;
    }

    /**
     * @param DataTableBuildConfig $buildConfig
     *
     * @return bool
     */
    public function canFetchData(DataTableBuildConfig $buildConfig)
    {
        return $this->fetcher->canFetchData($buildConfig);
    }

    /**
     * @param DataTableBuildConfig $buildConfig
     *
     * @return DataTableDataResult
     */
    public function fetchData(DataTableBuildConfig $buildConfig)
    {
        $dataTable = $this->buildTable($buildConfig);

        return $this->fetcher->fetchData($dataTable);
    }

    /**
     * @param DataTableBuildConfig $buildConfig
     *
     * @return bool
     */
    public function canExportData(DataTableBuildConfig $buildConfig)
    {
        return $this->fetcher->canExportData($buildConfig);
    }

    /**
     * @param DataTableBuildConfig $buildConfig
     *
     * @return string Path to temporary file
     */
    public function exportData(DataTableBuildConfig $buildConfig)
    {
        $dataTable = $this->buildTable($buildConfig);

        return $this->fetcher->exportData($dataTable);
    }

    /**
     * @param DataTableBuildConfig $buildConfig
     *
     * @return DataTableHtmlResult
     */
    public function renderHtml(DataTableBuildConfig $buildConfig)
    {
        $dataTable = $this->buildTable($buildConfig);

        return $this->renderer->createHtmlResult($dataTable);
    }

    /**
     * @param DataTableBuildConfig $buildConfig
     *
     * @return DataTableInterface
     */
    private function buildTable(DataTableBuildConfig $buildConfig)
    {
        return $this->builder->buildTable($buildConfig);
    }
}
