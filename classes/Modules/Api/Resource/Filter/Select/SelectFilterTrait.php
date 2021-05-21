<?php

namespace Xentral\Modules\Api\Resource\Filter\Select;

use Xentral\Components\Database\SqlQuery\SelectQuery;

trait SelectFilterTrait
{
    /** @var array $selectFilter */
    protected $selectFilter = [];

    /**
     * @param SelectQuery $query
     * @param array       $settings
     *
     * @return SelectQuery
     */
    protected function applySelectFilter(SelectQuery $query, array $settings)
    {
        foreach ($this->selectFilter as $filter) {
            /** @var SelectFilterInterface $filter */
            $query = $filter->applyFilter($query, $settings[$filter->getFilterType()]);
        }

        return $query;
    }

    /**
     * @param SelectFilterInterface $filter
     */
    public function registerSelectFilter(SelectFilterInterface $filter)
    {
        $this->selectFilter[] = $filter;
    }
}
