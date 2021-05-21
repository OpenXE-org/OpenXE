<?php

namespace Xentral\Widgets\DataTable\Feature;

use Xentral\Widgets\DataTable\Column\Column;
use Xentral\Widgets\DataTable\Column\ColumnCollection;
use Xentral\Widgets\DataTable\DataTableInterface;
use Xentral\Widgets\DataTable\Options\DataTableOptions;

/**
 * @see https://datatables.net/extensions/responsive/
 */
final class ResponsiveFeature implements DataTableFeatureInterface
{
    /** @var int PRIO_HIGHEST */
    const PRIO_HIGHEST = 1;

    /** @var int PRIO_HIGHER */
    const PRIO_HIGHER = 10;

    /** @var int PRIO_NORMAL */
    const PRIO_NORMAL = 100;

    /** @var int PRIO_LOWER */
    const PRIO_LOWER = 1000;

    /** @var int PRIO_LOWEST */
    const PRIO_LOWEST = 10000;

    /** @var array $responsiveProperty */
    private $responsiveProperty = [
        'details' => false,
    ];

    /** @var array $columnPriorities */
    private $columnPriorities = [];

    /** @var int $defaultPriority */
    private $defaultPriority = self::PRIO_NORMAL;

    /**
     * @param DataTableInterface $table
     *
     * @return void
     */
    public function modifyTable(DataTableInterface $table)
    {
        $this->modifyOptions($table->getOptions());
        $this->modifyColumns($table->getColumns());
    }

    /**
     * @param string $columnName
     * @param int    $priority
     *
     * @return void
     */
    public function setPriority($columnName, $priority)
    {
        $this->columnPriorities[$columnName] = (int)$priority;
    }

    /**
     * @param int $priority
     *
     * @return void
     */
    public function setDefaultPriority($priority)
    {
        $this->defaultPriority = (int)$priority;
    }

    /**
     * @param DataTableOptions $options
     *
     * @return void
     */
    private function modifyOptions(DataTableOptions $options)
    {
        $options->setOption('responsive', $this->responsiveProperty);
        $options->removeOption('scrollX');
    }

    /**
     * @param ColumnCollection $columns
     *
     * @return void
     */
    private function modifyColumns(ColumnCollection $columns)
    {
        /** @var Column $column */
        foreach ($columns as $column) {
            $name = $column->getName();
            if (isset($this->columnPriorities[$name])) {
                $column->set('responsivePriority', $this->columnPriorities[$name]);
            } else {
                if (!$column->has('responsivePriority')) {
                    $column->set('responsivePriority', $this->defaultPriority);
                }
            }
        }
    }
}
