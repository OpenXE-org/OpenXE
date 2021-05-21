<?php

namespace Xentral\Modules\Api\Resource\Filter\Select;

use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Modules\Api\Exception\InvalidArgumentException;

class SimpleSearchFilter implements SelectFilterInterface
{
    /** @var array $registeredProperties */
    protected $registeredProperties;

    /**
     * @param array $search
     */
    public function __construct(array $search)
    {
        $this->registeredProperties = $search;
    }

    /**
     * @param SelectQuery $query
     * @param array       $filter
     *
     * @return SelectQuery
     */
    public function applyFilter(SelectQuery $query, array $filter)
    {
        return $this->appendFilterQuery($query, $filter);
    }

    /**
     * @return string
     */
    public function getFilterType()
    {
        return SelectFilterInterface::TYPE_SEARCHING;
    }

    /**
     * @param SelectQuery $select
     * @param array       $filter
     *
     * @return SelectQuery
     */
    protected function appendFilterQuery(SelectQuery $select, array $filter)
    {
        // Kein Filter verwendet
        if (empty($filter)) {
            return $select;
        }

        // Filter an SelectQuery anfügen
        foreach ($filter as $property => $value) {

            // $_GET['filter'] wird von ComplexSearchFilter verarbeitet
            /* @see \Xentral\Modules\Api\Resource\Filter\Select\ComplexSearchFilter */
            if ($property === 'filter') {
                continue;
            }

            $filterProperty = $this->prepareFilterName($property);
            $filterValue = $this->prepareFilterValue($property, $value);

            $select->where(sprintf('%s :%s', $filterProperty, $property));
            $select->bindValue((string)$property, $filterValue);
        }

        return $select;
    }

    /**
     * @param string  $filterName
     *
     * @return string
     */
    protected function prepareFilterName($filterName)
    {
        $filterProperty = trim($this->getRegisteredProperty($filterName));
        $filterProperty = str_replace('%', '', $filterProperty); // Prozent aus LIKE-Suche entfernen

        return $filterProperty;
    }

    /**
     * @param string $filterName
     * @param mixed  $filterValue
     *
     * @return mixed
     */
    protected function prepareFilterValue($filterName, $filterValue)
    {
        $filterProperty = trim($this->getRegisteredProperty($filterName));

        // LIKE-Suche aufbereiten
        if (substr($filterProperty, -6) === ' LIKE%') {
            $filterValue = "{$filterValue}%";
        }
        if (substr($filterProperty, -6) === ' %LIKE') {
            $filterValue = "%{$filterValue}";
        }
        if (substr($filterProperty, -7) === ' %LIKE%') {
            $filterValue = "%{$filterValue}%";
        }

        return $filterValue;
    }

    /**
     * @param string $param
     *
     * @return string
     */
    protected function getRegisteredProperty($param)
    {
        if (!isset($this->registeredProperties[$param])) {
            throw new InvalidArgumentException(
                sprintf('Search parameter "%s" is not supported.', $param)
            );
        }

        return $this->registeredProperties[$param];
    }
}
