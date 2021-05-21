<?php

namespace Xentral\Widgets\DataTable\Filter;

use Xentral\Widgets\DataTable\DataTableInterface;
use Xentral\Widgets\DataTable\Request\DataTableRequest;

abstract class AbstractFilter implements FilterInterface
{
    /** @var string $type */
    protected $type;

    /**
     * @return string
     */
    abstract public function getType();

    /**
     * @param DataTableInterface $table
     * @param DataTableRequest   $request
     *
     * @return void
     */
    abstract public function applyFilter(DataTableInterface $table, DataTableRequest $request);
}
