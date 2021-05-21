<?php

namespace Xentral\Widgets\DataTable\Feature;

use Xentral\Widgets\DataTable\DataTableInterface;
use Xentral\Widgets\DataTable\Exception\ColumnNotFoundException;
use Xentral\Widgets\DataTable\Exception\InvalidArgumentException;

/**
 * @see https://datatables.net/extensions/rowgroup/
 */
final class RowGroupFeature implements DataTableFeatureInterface
{
    /** @var array $groupColumns */
    private $groupColumns;

    /** @var bool $enabled */
    private $enabled;

    /**
     * @param array $columnNames
     */
    public function __construct(array $columnNames)
    {
        if (count($columnNames) === 0) {
            throw new InvalidArgumentException('Parameter "columnNames" is can not be empty.');
        }
        $this->groupColumns = $columnNames;
        $this->enabled = true;
    }

    /**
     * @param DataTableInterface $table
     *
     * @throws ColumnNotFoundException
     *
     * @return void
     */
    public function modifyTable(DataTableInterface $table)
    {
        foreach ($this->groupColumns as $columnName) {
            if (!$table->getColumns()->has($columnName)) {
                throw new ColumnNotFoundException(sprintf(
                    'RowGroupFeature failed. Column "%s" not found.',
                    $columnName
                ));
            }
        }


        if ($this->enabled === true) {
            $table->getOptions()->setOption('rowGroup', ['dataSrc' => $this->groupColumns]);
        }
        if ($this->enabled === false) {
            $table->getOptions()->setOption('rowGroup', false);
        }
    }

    /**
     * @param array $columnNames
     *
     * @return void
     */
    public function groupBy(array $columnNames)
    {
        if (count($columnNames) === 0) {
            throw new InvalidArgumentException('Parameter "columnNames" is can not be empty.');
        }
        $this->groupColumns = $columnNames;
    }

    /**
     * @return void
     */
    public function enable()
    {
        $this->enabled = true;
    }

    /**
     * @return void
     */
    public function disable()
    {
        $this->enabled = false;
    }
}
