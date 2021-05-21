<?php

namespace Xentral\Widgets\DataTable\Feature;

use Xentral\Widgets\DataTable\DataTableInterface;

final class TableStylingFeature implements DataTableFeatureInterface
{
    /** @var array $cssClasses */
    private $cssClasses = [];

    /**
     * @param bool $compact
     * @param bool $disableLineWrapping
     */
    public function __construct($compact = false, $disableLineWrapping = false)
    {
        $this->setDefaultStyle();
        if ($compact === true) {
            $this->setCompactStyle();
        }
        if ($disableLineWrapping === true) {
            $this->disableLineWrapping();
        } else {
            $this->enableLineWrapping();
        }
    }

    /**
     * @param DataTableInterface $table
     *
     * @return void
     */
    public function modifyTable(DataTableInterface $table)
    {
        foreach ($this->cssClasses as $cssClass) {
            $table->getConfig()->addCssClass($cssClass);
        }
    }

    /**
     * display: Short-hand for stripe, hover, row-border and order-column.
     *
     * @return void
     */
    public function setDefaultStyle()
    {
        $this->removeCssClass('display');
        $this->removeCssClass('compact');
        $this->removeCssClass('order-column');

        $this->enableHover();
        $this->disableRowBorder();
        $this->disableStripes();
    }

    /**
     * @return void
     */
    public function setCompactStyle()
    {
        $this->addCssClass('compact');
    }

    /**
     * @return void
     */
    public function enableLineWrapping()
    {
        $this->removeCssClass('nowrap');
    }

    /**
     * @return void
     */
    public function disableLineWrapping()
    {
        $this->addCssClass('nowrap');
    }

    /**
     * @return void
     */
    public function enableHover()
    {
        $this->addCssClass('hover');
    }

    /**
     * @return void
     */
    public function disableHover()
    {
        $this->removeCssClass('hover');
    }

    /**
     * @return void
     */
    public function enableStripes()
    {
        $this->addCssClass('stripe');
    }

    /**
     * @return void
     */
    public function disableStripes()
    {
        $this->removeCssClass('stripe');
    }

    /**
     * @return void
     */
    public function enableRowBorder()
    {
        $this->addCssClass('row-border');
        $this->removeCssClass('cell-border');
    }

    /**
     * @return void
     */
    public function disableRowBorder()
    {
        $this->removeCssClass('row-border');
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    private function hasCssClass($className)
    {
        return in_array($className, $this->cssClasses, true);
    }

    /**
     * @param string $className
     *
     * @return void
     */
    private function addCssClass($className)
    {
        $this->cssClasses[] = trim($className);
        $this->cssClasses = array_unique($this->cssClasses);
    }

    /**
     * @param string $className
     *
     * @return void
     */
    private function removeCssClass($className)
    {
        $classKey = array_search($className, $this->cssClasses, true);
        if ($classKey !== false) {
            unset($this->cssClasses[$classKey]);
            $this->cssClasses = array_values($this->cssClasses);
        }
    }
}
