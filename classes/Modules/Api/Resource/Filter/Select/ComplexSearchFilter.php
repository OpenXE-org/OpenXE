<?php

namespace Xentral\Modules\Api\Resource\Filter\Select;

use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Modules\Api\Exception\InvalidArgumentException;

class ComplexSearchFilter implements SelectFilterInterface
{
    public function __construct()
    {
    }

    /**
     * @param SelectQuery $query
     * @param array       $filter
     *
     * @return SelectQuery
     */
    public function applyFilter(SelectQuery $query, array $filter)
    {
        $filterParams = isset($filter['filter']) && is_array($filter['filter']) ? $filter['filter'] : [];

        // Komplexe Suchfilter mit Klammern umschließen
        return $query->where(function ($inner) use ($filterParams) {
            $this->appendFilterQuery($inner, $filterParams);
        });
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

        // Spalten aus Query holen
        $cols = $select->getCols();

        // Filter an SelectQuery anfügen
        foreach ($filter as $index => $item) {

            if (empty($item['property']) && empty($item['value'])) {
                throw new InvalidArgumentException('Filter not valid. "property" und "value" required.');
            }

            // Defaults für optionale Felder setzen
            if (empty($item['expression'])) {
                $item['expression'] = 'LIKE';
            }
            if (empty($item['operation'])) {
                $item['operation'] = 'AND';
            }

            // Aliase ersetzen
            // Notwendig für Properties die einen Alias haben.
            // Nach Alias-Feldnamen kann nicht gesucht werden.
            if (array_key_exists($item['property'], $cols)) {
                $item['property'] = $cols[$item['property']];
            }

            switch (strtolower($item['expression'])) {
                case 'eq':
                    $item['expression'] = '=';
                    break;
                case 'not':
                    $item['expression'] = '!=';
                    break;
                case 'lt':
                    $item['expression'] = '<';
                    break;
                case 'lte':
                    $item['expression'] = '<=';
                    break;
                case 'gt':
                    $item['expression'] = '>';
                    break;
                case 'gte':
                    $item['expression'] = '>=';
                    break;
                case 'like':
                    $item['expression'] = 'LIKE';
                    break;
                case 'not_like':
                    $item['expression'] = 'NOT LIKE';
                    break;
                default:
                    $item['expression'] = 'LIKE';
                    break;
            }

            if (strtoupper($item['operation']) === 'OR') {
                $select->orWhere(sprintf('%s %s ?', $item['property'], $item['expression']), $item['value']);
            } else {
                $select->where(sprintf('%s %s ?', $item['property'], $item['expression']), $item['value']);
            }
        }

        return $select;
    }
}
