<?php

namespace Xentral\Widgets\DataTable\Feature;

use Xentral\Widgets\DataTable\DataTableInterface;
use Xentral\Widgets\DataTable\Exception\DataTableExceptionInterface;

interface DataTableFeatureInterface
{
    /**
     * @param DataTableInterface $table
     *
     * @throws DataTableExceptionInterface
     *
     * @return void
     */
    public function modifyTable(DataTableInterface $table);
}
