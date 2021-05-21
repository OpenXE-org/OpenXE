<?php

namespace Xentral\Widgets\DataTable\Type;

use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Widgets\DataTable\Column\ColumnCollection;
use Xentral\Widgets\DataTable\Options\DataTableOptions;
use Xentral\Widgets\DataTable\Feature\FeatureCollection;
use Xentral\Widgets\DataTable\Filter\FilterCollection;

interface DataTableTypeInterface
{
    /** @var string|null PARENT_TABLE */
    const PARENT_TABLE = null;

    /**
     * @param DataTableOptions $options
     *
     * @return void
     */
    public function configureOptions(DataTableOptions $options);

    /**
     * @param SelectQuery $query
     *
     * @return void
     */
    public function configureQuery(SelectQuery $query);

    /**
     * @param ColumnCollection $columns
     *
     * @return void
     */
    public function configureColumns(ColumnCollection $columns);

    /**
     * @param FeatureCollection $features
     *
     * @return void
     */
    public function configureFeatures(FeatureCollection $features);

    /**
     * @param FilterCollection $filters
     *
     * @return void
     */
    public function configureFilters(FilterCollection $filters);
}
