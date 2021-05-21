<?php

namespace Xentral\Widgets\DataTable\Feature;

use Xentral\Widgets\DataTable\Column\Column;
use Xentral\Widgets\DataTable\DataTableInterface;

/**
 * @todo Fertigstellen
 */
final class ColumnFilterFeature implements DataTableFeatureInterface
{
    /** @var string TYPE_NONE Filter deactivated */
    const TYPE_NONE = 'none'; // Filter deactivated

    /** @var string TYPE_TEXT Default filter */
    const TYPE_TEXT = 'text'; // Default

    /** @var string TYPE_TEXT_MULTI @todo Mehrere Wörter mit ODER suchen */
    const TYPE_TEXT_MULTI = 'text_multi';

    /** @var string TYPE_SELECT @todo Dropdown */
    const TYPE_SELECT = 'select';

    /** @var string TYPE_NUMBER */
    const TYPE_NUMBER = 'number';

    /** @var string TYPE_NUMBER_RANGE */
    const TYPE_NUMBER_RANGE = 'number_range';

    /** @var string TYPE_DATE @todo */
    const TYPE_DATE = 'date';

    /** @var string TYPE_DATE_RANGE @todo */
    const TYPE_DATE_RANGE = 'date_range';

    /** @var array $columnSettings */
    private $columnSettings = [];

    /**
     * @param DataTableInterface $table
     *
     * @return void
     */
    public function modifyTable(DataTableInterface $table)
    {
        $this->modifyOptions($table);
    }

    /**
     * @param string $columnName
     *
     * @return void
     */
    public function addNumberRangeFilter($columnName)
    {
        $this->columnSettings[$columnName] = [
            'name' => $columnName,
            'type' => self::TYPE_NUMBER_RANGE,
        ];
    }

    /**
     * @todo
     *
     * @param string $columnName
     *
     * @return void
     */
    public function addMultiWordFilter($columnName)
    {
        $this->columnSettings[$columnName] = [
            'name'       => $columnName,
            'type'       => self::TYPE_TEXT,
            'multi_word' => true,
        ];
    }

    /**
     * @todo
     *
     * @param string $columnName
     *
     * @return void
     */
//    public function addDropdownFilter($columnName)
//    {
//        $this->columnSettings[$columnName] = [
//            'name' => $columnName,
//            'type' => self::TYPE_SELECT,
//        ];
//    }

    /**
     * @param DataTableInterface $table
     *
     * @return void
     */
    private function modifyOptions(DataTableInterface $table)
    {
        $result = [];
        /** @var Column $column */
        foreach ($table->getColumns() as $index => $column) {
            $columnName = $column->getName();
            if (isset($this->columnSettings[$columnName])) {
                $result[$index] = $this->columnSettings[$columnName];
            } else {
                // Default
                $defaultType = $column->isSearchable() ? self::TYPE_TEXT : self::TYPE_NONE;
                $result[$index] = [
                    'name' => $column->getName(),
                    'type' => $defaultType,
                ];
            }
            if ($columnName === 'id' || $columnName === 'menu') {
                $result[$index] = [
                    'name' => $column->getName(),
                    'type' => self::TYPE_NONE, // Column filtering inactive on menu and id column
                ];
            }
            if (strpos($columnName, '_') === 0) {
                $result[$index] = [
                    'name' => $column->getName(),
                    'type' => self::TYPE_NONE, // @todo Wird benötigt?
                ];
            }
        }

        $table->getOptions()->setOption('columnFilter', $result);
    }
}
