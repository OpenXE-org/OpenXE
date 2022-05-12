<?php

namespace Xentral\Widgets\DataTable\Filter;

use Xentral\Widgets\DataTable\DataTableInterface;
use Xentral\Widgets\DataTable\Exception\ColumnNotFoundException;
use Xentral\Widgets\DataTable\Exception\InvalidArgumentException;
use Xentral\Widgets\DataTable\Request\DataTableRequest;

class SingleWordTextFilter implements FilterInterface
{
    /** @var string LIKE_EQUALS */
    const LIKE_EQUALS = 'equals';

    /** @var string LIKE_STARTS_WITH */
    const LIKE_STARTS_WITH = 'startswith';

    /** @var string LIKE_ENDS_WITH */
    const LIKE_ENDS_WITH = 'endswith';

    /** @var string LIKE_ANY */
    const LIKE_ANY = 'any';

    /** @var string $columnName*/
    private $columnName;

    /** @var string $filterName */
    private $filterName;

    /** @var string $likePattern */
    private $likePattern;

    /**
     * @param string      $columnName
     * @param string      $filterName
     * @param string|null $likePattern
     */
    public function __construct($columnName, $filterName, $likePattern = self::LIKE_ANY)
    {
        if ($likePattern !== null) {
            $validLikePatterns = [self::LIKE_EQUALS, self::LIKE_STARTS_WITH, self::LIKE_ENDS_WITH, self::LIKE_ANY];
            if (!in_array($likePattern, $validLikePatterns, true)) {
                throw new InvalidArgumentException(sprintf(
                    'Like pattern "%s" is invalid. Valid patterns are: %s', $likePattern,
                    implode(', ', $validLikePatterns)
                ));
            }
        }

        $this->columnName = $columnName;
        $this->filterName = $filterName;
        $this->likePattern = $likePattern;
    }

    /**
     * @return string
     */
    public function getFilterName()
    {
        return $this->filterName;
    }

    /**
     * @return string|null
     */
    public function getLikePattern()
    {
        return $this->likePattern;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return FilterInterface::TYPE_TEXT;
    }

    /**
     * @param DataTableInterface $table
     * @param DataTableRequest   $request
     *
     * @throws ColumnNotFoundException
     *
     * @return void
     */
    public function applyFilter(DataTableInterface $table, DataTableRequest $request)
    {
        $column = $table->getColumns()->getByName($this->columnName);
        if ($column === null || $column->getDbColumn() === null) {
            throw new ColumnNotFoundException(sprintf(
                'Can not apply text filter. Column "%s" is missing.',
                $this->columnName
            ));
        }

        $filterValues = $request->getParams()->getFilterValues();
        if (!array_key_exists($this->filterName, $filterValues)) {
            return; // Filter param is not set
        }

        $filterValue = (string)$filterValues[$this->filterName];
        if ($filterValue === '') {
            return; // Filter value is empty
        }

        switch ($this->likePattern) {
            case self::LIKE_EQUALS:
                $filterCondition = $filterValue;
                break;
            case self::LIKE_STARTS_WITH:
                $filterCondition = $filterValue . '%';
                break;
            case self::LIKE_ENDS_WITH:
                $filterCondition = '%' . $filterValue;
                break;
            case self::LIKE_ANY:
            default:
                $filterCondition = '%' . $filterValue . '%';
                break;
        }

        $table->getBaseQuery()->where($column->getDbColumn() . ' LIKE ?', $filterCondition);
    }
}
