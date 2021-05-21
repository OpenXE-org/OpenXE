<?php

namespace Xentral\Widgets\DataTable;

use Closure;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Widgets\DataTable\Column\ColumnCollection;
use Xentral\Widgets\DataTable\Options\DataTableOptions;
use Xentral\Widgets\DataTable\Feature\FeatureCollection;
use Xentral\Widgets\DataTable\Filter\FilterCollection;

final class PreparedDataTable implements DataTableInterface
{
    /** @var DataTableBuildConfig $config */
    private $config;

    /** @var SelectQuery $query */
    private $query;

    /** @var DataTableOptions $options */
    private $options;

    /** @var ColumnCollection $columns */
    private $columns;

    /** @var FeatureCollection $features */
    private $features;

    /** @var FilterCollection $filters */
    private $filters;

    /** @var Closure|null $customSearch @todo */
    private $customSearch;

    /**
     * @param DataTableBuildConfig $config
     * @param DataTableOptions     $options
     * @param SelectQuery          $selectQuery
     * @param ColumnCollection     $columns
     * @param FeatureCollection    $features
     * @param FilterCollection     $filters
     */
    public function __construct(
        DataTableBuildConfig $config,
        DataTableOptions $options,
        SelectQuery $selectQuery,
        ColumnCollection $columns,
        FeatureCollection $features,
        FilterCollection $filters
    ) {
        $this->config = $config;
        $this->options = $options;
        $this->query = $selectQuery;
        $this->columns = $columns;
        $this->features = $features;
        $this->filters = $filters;
    }

    /**
     * @return DataTableBuildConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return DataTableOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return SelectQuery
     */
    public function getBaseQuery()
    {
        return $this->query;
    }

    /**
     * @return ColumnCollection
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return FeatureCollection
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * @return FilterCollection
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return Closure|null @todo
     */
    public function getCustomSearch()
    {
        return $this->customSearch;
    }
}
