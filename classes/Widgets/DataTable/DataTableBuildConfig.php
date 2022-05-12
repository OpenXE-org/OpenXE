<?php

namespace Xentral\Widgets\DataTable;

use Xentral\Widgets\DataTable\Exception\InvalidArgumentException;
use Xentral\Widgets\DataTable\Type\DataTableTypeInterface;

final class DataTableBuildConfig
{
    /** @var string $tableName */
    private $tableName;

    /** @var string $tableClass */
    private $tableClass;

    /** @var string $ajaxUrl */
    private $ajaxUrl;

    /** @var string $ajaxMethod */
    private $ajaxMethod;

    /** @var array $ajaxParams Additional AJAX parameter */
    private $ajaxParams;

    /** @var bool $autoInit */
    private $autoInit;

    /**
     * Available DataTable classes: display, compact, hover, order-column, row-border, cell-border, stripe, nowrap
     *
     * display = Short-hand for stripe, hover, row-border and order-column.
     *
     * @see https://datatables.net/manual/styling/classes#Table-classes
     *
     * @var array $cssClasses
     */
    private $cssClasses = [];

    /**
     * @param string $tableName  Unique table name; Will be used as id-attribute on <table> element
     * @param string $tableClass FQCN of DataTable class that implements DataTableTypeInterface
     * @param string $ajaxUrl
     * @param bool   $autoInit
     */
    public function __construct($tableName, $tableClass, $ajaxUrl, $autoInit = true)
    {
        if (!class_exists($tableClass, true)) {
            throw new InvalidArgumentException(sprintf('DataTable class "%s" not found', $tableClass));
        }
        $interfaces = class_implements($tableClass, true);
        if (!in_array(DataTableTypeInterface::class, $interfaces, true)) {
            throw new InvalidArgumentException('DataTable class does not implement %s', DataTableTypeInterface::class);
        }
        $tableNameCleaned = preg_replace('/[^a-z0-9_-]+/', '', $tableName);
        if ($tableNameCleaned !== $tableName) {
            throw new InvalidArgumentException(sprintf(
                'Table name "%s" contains illegal characters. ' .
                'Valid characters are: a-z, 0-9, hyphens and underscores.',
                $tableName
            ));
        }


        $this->tableName = $tableName;
        $this->tableClass = $tableClass;
        $this->ajaxUrl = $ajaxUrl;
        $this->ajaxMethod = 'GET';
        $this->ajaxParams = ['tablename' => $tableName];
        $this->autoInit = $autoInit;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getTableClass()
    {
        return $this->tableClass;
    }

    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->ajaxUrl;
    }

    /**
     * @return string
     */
    public function getAjaxMethod()
    {
        return $this->ajaxMethod;
    }

    /**
     * @param string $method [GET|POST]
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function setAjaxMethod($method)
    {
        $method = strtoupper($method);
        if (!in_array($method, ['GET', 'POST'], true)) {
            throw new InvalidArgumentException('AJAX method "%s" is invalid.', $method);
        }

        $this->ajaxMethod = $method;
    }

    /**
     * @return array
     */
    public function getAjaxParams()
    {
        return $this->ajaxParams;
    }

    /**
     * @param string $param
     * @param mixed  $value
     *
     * @throws InvalidArgumentException
     */
    public function setAjaxParam($param, $value)
    {
        $cleanedName = (string)preg_replace('#[^A-Za-z0-9_-]#', '', trim($param));
        if ($cleanedName !== $param) {
            throw new InvalidArgumentException(sprintf(
                'AJAX parameter name "%s" contains illegal characters. ' .
                'Valid characters are: a-z, 0-9, hyphens and underscores.',
                $param
            ));
        }
        if ($param === 'tablename') {
            throw new InvalidArgumentException('AJAX parameter "tablename" is reserved.');
        }

        $this->ajaxParams[$param] = $value;
    }

    /**
     * @return bool
     */
    public function isAutoInit()
    {
        return $this->autoInit;
    }

    /**
     * @return string
     */
    public function getCssClassesString()
    {
        return implode(' ', $this->getCssClasses());
    }

    /**
     * @return array
     */
    public function getCssClasses()
    {
        return $this->cssClasses;
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    public function hasCssClass($className)
    {
        return in_array($className, $this->cssClasses, true);
    }

    /**
     * @param string $className
     *
     * @return void
     */
    public function addCssClass($className)
    {
        $cleanedName = (string)preg_replace('#[^a-z0-9_-]#', '', trim($className));
        if ($cleanedName !== $className) {
            throw new InvalidArgumentException(sprintf(
                'CSS class name "%s" contains illegal characters. ' .
                'Valid characters are: a-z, 0-9, hyphens and underscores.',
                $className
            ));
        }

        $this->cssClasses[] = $cleanedName;
        $this->cssClasses = array_unique($this->cssClasses);
    }

    /**
     * @param string $className
     *
     * @return void
     */
    public function removeCssClass($className)
    {
        $classKey = array_search($className, $this->cssClasses, true);
        if ($classKey !== false) {
            unset($this->cssClasses[$classKey]);
            $this->cssClasses = array_values($this->cssClasses);
        }
    }
}
