<?php

namespace Xentral\Modules\Api\Resource\Filter\Select;

use Xentral\Components\Database\SqlQuery\SelectQuery;

interface SelectFilterInterface
{
    const TYPE_SORTING = 'sort';
    const TYPE_SEARCHING = 'search';

    /**
     * @param SelectQuery $query
     * @param array       $filterParams
     *
     * @return SelectQuery
     */
    public function applyFilter(SelectQuery $query, array $filterParams);

    /**
     * @return string
     */
    public function getFilterType();
}
