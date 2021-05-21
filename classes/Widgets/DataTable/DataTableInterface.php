<?php

namespace Xentral\Widgets\DataTable;

use Closure;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Widgets\DataTable\Column\ColumnCollection;
use Xentral\Widgets\DataTable\Options\DataTableOptions;
use Xentral\Widgets\DataTable\Feature\FeatureCollection;
use Xentral\Widgets\DataTable\Filter\FilterCollection;

interface DataTableInterface
{
    /**
     * @return DataTableBuildConfig
     */
    public function getConfig();

    /**
     * @return DataTableOptions
     */
    public function getOptions();

    /**
     * @return ColumnCollection
     */
    public function getColumns();

    /**
     * @return FeatureCollection
     */
    public function getFeatures();

    /**
     * @return SelectQuery
     */
    public function getBaseQuery();

    /**
     * @return Closure|null
     */
    public function getCustomSearch();

    /**
     * @return FilterCollection
     */
    public function getFilters();
}
