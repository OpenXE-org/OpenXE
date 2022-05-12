<?php

namespace Xentral\Widgets\DataTable\Filter;

use Closure;
use Xentral\Widgets\DataTable\DataTableInterface;
use Xentral\Widgets\DataTable\Request\DataTableRequest;

final class CustomFilter implements FilterInterface
{
    /** @var Closure $closure */
    private $closure;

    /**
     * @param Closure $closure
     */
    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return FilterInterface::TYPE_CUSTOM;
    }

    /**
     * @param DataTableInterface $table
     * @param DataTableRequest   $request
     *
     * @return void
     */
    public function applyFilter(DataTableInterface $table, DataTableRequest $request)
    {
        $closure = $this->closure;
        $closure($table->getBaseQuery(), $request);
    }
}
