<?php

namespace Xentral\Widgets\DataTable\Filter;

use Xentral\Widgets\DataTable\DataTableInterface;
use Xentral\Widgets\DataTable\Request\DataTableRequest;

interface FilterInterface
{
    /** @var string TYPE_TEXT */
    const TYPE_TEXT = 'text';

    /** @var string TYPE_NUMBER */
    const TYPE_NUMBER = 'number';

    /** @var string TYPE_NUMBER_RANGE */
    const TYPE_NUMBER_RANGE = 'number_range';

    /** @var string TYPE_CUSTOM */
    const TYPE_CUSTOM = 'custom';

    /**
     * @return string
     */
    public function getType();

    /**
     * @param DataTableInterface $table
     * @param DataTableRequest   $request
     *
     * @return void
     */
    public function applyFilter(DataTableInterface $table, DataTableRequest $request);
}
