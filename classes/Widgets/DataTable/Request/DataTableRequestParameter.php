<?php

namespace Xentral\Widgets\DataTable\Request;

use Xentral\Components\Http\Request;

/**
 * @see https://datatables.net/manual/server-side
 */
final class DataTableRequestParameter
{
    /** @var string|null $tableName */
    private $tableName;

    /** @var int $draw Draw counter */
    private $draw;

    /** @var int $start Paging first record offset */
    private $start;

    /** @var int $length Number of records returned */
    private $length;

    /** @var array $columns Column settings and search queries */
    private $columns;

    /** @var array $search Global search query */
    private $search;

    /** @var array $order Ordering settings */
    private $order;

    /** @var array $filter Custom parameter for filter feature */
    private $filter;

    /** @var array $export Custom parameter for export feature */
    private $export;

    /**
     * @param string $tableName
     * @param int    $draw
     * @param int    $start
     * @param int    $length
     * @param array  $columns
     * @param array  $search
     * @param array  $order
     * @param array  $filter
     * @param array  $export
     */
    public function __construct(
        $tableName = null,
        $draw = 1,
        $start = 0,
        $length = 10,
        $columns = [],
        $search = [],
        $order = [],
        $filter = [],
        $export = []
    ) {
        $this->tableName = $tableName;
        $this->draw = (int)$draw;
        $this->start = (int)$start;
        $this->length = (int)$length;
        $this->columns = (array)$columns;
        $this->search = (array)$search;
        $this->order = (array)$order;
        $this->filter = (array)$filter;
        $this->export = (array)$export;
    }

    /**
     * @param Request $request
     *
     * @return self
     */
    public static function fromRequest(Request $request)
    {
        $params = $request->getMethod() === 'GET' ? $request->get : $request->post;

        $tableName = $params->getAlphaNumWithDashes('tablename', null);
        $draw = $params->getInt('draw', 1);
        $start = $params->getInt('start', 0);
        $length = $params->getInt('length', 10);
        $columns = (array)$params->get('columns', []);
        $search = (array)$params->get('search', []);
        $order = (array)$params->get('order', []);
        $filter = (array)$params->get('filter', []);
        $export = (array)$params->get('export', []);

        return new self($tableName, $draw, $start, $length, $columns, $search, $order, $filter, $export);
    }

    /**
     * @return string|null
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return int
     */
    public function getDraw()
    {
        return $this->draw;
    }

    /**
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return array
     */
    public function getSearchValues()
    {
        return $this->search;
    }

    /**
     * @return array
     */
    public function getOrderValues()
    {
        return $this->order;
    }

    /**
     * @return array
     */
    public function getColumnsValues()
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getFilterValues()
    {
        return $this->filter;
    }

    /**
     * @return array
     */
    public function getExportValues()
    {
        return $this->export;
    }
}

