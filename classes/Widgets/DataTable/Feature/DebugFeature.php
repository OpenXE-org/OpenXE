<?php

namespace Xentral\Widgets\DataTable\Feature;

use Xentral\Widgets\DataTable\DataTableInterface;

final class DebugFeature implements DataTableFeatureInterface
{
    /** @var bool $enabled */
    private $enabled;

    /**
     * @param bool $enabled
     */
    public function __construct($enabled = true)
    {
        $this->enabled = (bool)$enabled;
    }

    /**
     * @param DataTableInterface $table
     *
     * @return void
     */
    public function modifyTable(DataTableInterface $table)
    {
        if ($this->enabled === true) {
            $table->getConfig()->addCssClass('datatable-debug');
        } else {
            $table->getConfig()->removeCssClass('datatable-debug');
        }
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
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
