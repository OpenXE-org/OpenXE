<?php

namespace Xentral\Modules\Api\Resource\Feature;

use Xentral\Modules\Api\Resource\Filter\Select\SortingFilter;

trait SortingFeatureTrait
{
    /**
     * Festlegen welche Sortierungen erlaubt sind
     *
     * @example $this->registerSortingParams([
     *              'bezeichnung' => 'k.bezeichnung',
     *              'projekt' => 'k.projekt',
     *              'parent' => 'k.parent',
     *          ]);
     *
     * @param array $params
     */
    protected function registerSortingParams(array $params)
    {
        $this->registerSelectFilter(new SortingFilter($params));
    }
}
