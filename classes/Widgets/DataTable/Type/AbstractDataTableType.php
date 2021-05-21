<?php

namespace Xentral\Widgets\DataTable\Type;

use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Widgets\DataTable\Column\ColumnCollection;
use Xentral\Widgets\DataTable\Feature\DataTableFeatureInterface;
use Xentral\Widgets\DataTable\Feature\FeatureCollection;
use Xentral\Widgets\DataTable\Feature\ResponsiveFeature;
use Xentral\Widgets\DataTable\Feature\StateSaveFeature;
use Xentral\Widgets\DataTable\Feature\TableControlFeature;
use Xentral\Widgets\DataTable\Feature\TableStylingFeature;
use Xentral\Widgets\DataTable\Filter\FilterCollection;
use Xentral\Widgets\DataTable\Options\DataTableOptions;

abstract class AbstractDataTableType implements DataTableTypeInterface
{
    /**
     * @param DataTableOptions $options
     *
     * @return void
     */
    public function configureOptions(DataTableOptions $options)
    {
    }

    /**
     * @param SelectQuery $query
     *
     * @return void
     */
    public function configureQuery(SelectQuery $query)
    {
    }

    /**
     * @param ColumnCollection $columns
     *
     * @return void
     */
    public function configureColumns(ColumnCollection $columns)
    {
    }

    /**
     * @param FeatureCollection $features
     *
     * @return void
     */
    public function configureFeatures(FeatureCollection $features)
    {
        $this->addDefaultFeatures($features);
    }

    /**
     * @param FilterCollection $filters
     *
     * @return void
     */
    public function configureFilters(FilterCollection $filters)
    {
    }

    /**
     * @param FeatureCollection $featureCollection
     *
     * @return void
     */
    protected function addDefaultFeatures(FeatureCollection $featureCollection)
    {
        foreach ($this->getDefaultFeatures() as $defaultFeature) {
            $defaultFeatureClassName = get_class($defaultFeature);
            if (!$featureCollection->has($defaultFeatureClassName)) {
                $featureCollection->add($defaultFeature);
            }
        }
    }

    /**
     * @return DataTableFeatureInterface[]|array
     */
    private function getDefaultFeatures()
    {
        return [
            new StateSaveFeature($enabled = true, $duration = 0),
            new TableStylingFeature($compact = false, $noWrap = false),
            new TableControlFeature(),
            new ResponsiveFeature(),
        ];
    }
}
