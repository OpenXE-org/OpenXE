<?php

namespace Xentral\Modules\Api\Resource\Filter\Select;

use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Modules\Api\Exception\InvalidArgumentException;

class SortingFilter implements SelectFilterInterface
{
    /** @var array $sortingParams Erlaubte Sortierungs-Parameter */
    protected $sortingParams;

    /**
     * @param array $sortingParams
     */
    public function __construct(array $sortingParams)
    {
        $this->sortingParams = $sortingParams;
    }

    /**
     * @param SelectQuery $query
     * @param array       $filter
     *
     * @return SelectQuery
     */
    public function applyFilter(SelectQuery $query, array $filter)
    {
        return $this->appendSorting($query, $filter);
    }

    /**
     * @return string
     */
    public function getFilterType()
    {
        return SelectFilterInterface::TYPE_SORTING;
    }

    /**
     * @param array           $sorting
     * @param SelectQuery $selectQuery
     *
     * @return SelectQuery
     */
    protected function appendSorting(SelectQuery $selectQuery, array $sorting)
    {
        // Keine Sortier-Parameter vorhanden
        if (empty($sorting)) {
            return $selectQuery;
        }

        foreach ($sorting as $property => $direction) {
            if (is_int($property)) {
                $property = $direction;
                $direction = 'ASC';
            }

            $direction = strtoupper($direction);
            if (!in_array($direction, ['ASC', 'DESC'], true)) {
                throw new InvalidArgumentException(
                    sprintf('Sort direction "%s" is invalid', $direction)
                );
            }

            $dbProperty = $this->getRegisteredProperty($property);
            $selectQuery->orderBy([
                sprintf('%s %s', $dbProperty, $direction)
            ]);
        }

        return $selectQuery;
    }

    /**
     * @param string $property
     *
     * @return string
     */
    protected function getRegisteredProperty($property)
    {
        if (!isset($this->sortingParams[$property])) {
            throw new InvalidArgumentException(
                sprintf('Sorting parameter "%s" is not registered.', $property)
            );
        }

        return $this->sortingParams[$property];
    }
}
