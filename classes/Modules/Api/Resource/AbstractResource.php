<?php

namespace Xentral\Modules\Api\Resource;

use Exception;
use InvalidArgumentException;
use Xentral\Components\Database\Database;
use Xentral\Components\Database\SqlQuery\DeleteQuery;
use Xentral\Components\Database\SqlQuery\InsertQuery;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Components\Database\SqlQuery\UpdateQuery;
use Xentral\Modules\Api\Exception\ResourceNotFoundException;
use Xentral\Modules\Api\Resource\Exception\EndpointNotAvailableException;
use Xentral\Modules\Api\Resource\Feature\FilterFeatureTrait;
use Xentral\Modules\Api\Resource\Feature\IncludeFeatureTrait;
use Xentral\Modules\Api\Resource\Feature\SortingFeatureTrait;
use Xentral\Modules\Api\Resource\Feature\ValidationFeatureTrait;
use Xentral\Modules\Api\Resource\Filter\Select\ComplexSearchFilter;
use Xentral\Modules\Api\Resource\Filter\Select\SelectFilterInterface;
use Xentral\Modules\Api\Resource\Filter\Select\SelectFilterTrait;
use Xentral\Modules\Api\Resource\Result\CollectionResult;
use Xentral\Modules\Api\Resource\Result\ItemResult;
use Xentral\Modules\Api\Validator\Validator;

abstract class AbstractResource
{
    use SelectFilterTrait;

    use FilterFeatureTrait;
    use SortingFeatureTrait;
    use IncludeFeatureTrait;
    use ValidationFeatureTrait;

    /** @var Database $db */
    protected $db;

    /** @var Validator $validator */
    protected $validator;

    /** @return SelectQuery|false */
    abstract protected function selectAllQuery();

    /** @return SelectQuery|false */
    abstract protected function selectOneQuery();

    /** @return SelectQuery|false */
    abstract protected function selectIdsQuery();

    /** @return InsertQuery|false */
    abstract protected function insertQuery();

    /** @return UpdateQuery|false */
    abstract protected function updateQuery();

    /** @return UpdateQuery|DeleteQuery|false */
    abstract protected function deleteQuery();

    /** @return void */
    abstract protected function configure();

    /**
     * @param Database           $database
     * @param Validator          $validator
     */
    public function __construct(
        Database $database,
        Validator $validator
    ) {
        $this->db = $database;
        $this->validator = $validator;

        $this->configure();

        // Komplexe Suche immer aktivieren
        $this->registerSelectFilter(new ComplexSearchFilter());
    }

    /**
     * @param array $filter
     * @param array $sorting
     * @param array $columns
     * @param array $includes
     * @param int   $page
     * @param int   $paging
     *
     * @return CollectionResult
     */
    public function getList(
        array $filter = [],
        array $sorting = [],
        array $columns = [],
        array $includes = [],
        $page = 1,
        $paging = 20
    ) {
        /** @var SelectQuery $selectAll */
        $selectAll = $this->selectAllQuery();

        if (!$selectAll) {
            throw new EndpointNotAvailableException();
        }
        if (!$selectAll instanceof SelectQuery) {
            throw new InvalidArgumentException(sprintf(
                'selectAllQuery() must return an instance of %s', SelectQuery::class
            ));
        }

        // Suchfilter und Sortierung hinzufügen
        $selectAll = $this->applySelectFilter($selectAll, [
            SelectFilterInterface::TYPE_SEARCHING => $filter,
            SelectFilterInterface::TYPE_SORTING => $sorting,
        ]);

        // Filter hinzufügen
        //$selectAll = $this->appendFilterQuery($filter, $selectAll);
        //$bindValues = $this->appendFilterBindings($filter, $bindValues);

        // Sortierung hinzufügen
        //$selectAll = $this->appendSorting($sorting, $selectAll);

        /*echo "<pre>";
        echo $selectAll->getStatement();
        var_dump($selectAll->getBindValues());
        echo "</pre>";
        exit;*/

        // Ergebnisse ermitteln
        $selectList = clone $selectAll;
        if (!empty($columns)) {
            $selectList->resetCols()->cols($columns);
        }
        $selectList->page($page)->setPaging($paging);
        $items = $this->db->fetchAll(
            $selectList->getStatement(),
            $selectList->getBindValues()
        );

        if (count($items) === 0) {
            throw new ResourceNotFoundException();
        }

        // Gesamtanzahl der Ergebnisse ermitteln
        $selectCount = clone $selectAll;
        $selectCount->resetOrderBy()->resetCols()->cols(['COUNT(*)']);
        $total = (int)$this->db->fetchValue(
            $selectCount->getStatement(),
            $selectCount->getBindValues()
        );
        $pagination = $this->getPagination($total, count($items), $paging, $page);

        // Includes in Ergebnis integrieren
        $items = $this->integrateIncludes($includes, $items);

        return new CollectionResult($items, $pagination);
    }

    /**
     * @param array $ids
     * @param array $columns Spalten überschreiben
     *
     * @return CollectionResult
     */
    public function getIds(array $ids, array $columns = [])
    {
        /** @var SelectQuery $selectIds */
        $selectIds = $this->selectIdsQuery();
        if (!$selectIds) {
            throw new EndpointNotAvailableException();
        }
        if (!$selectIds instanceof SelectQuery) {
            throw new InvalidArgumentException(sprintf(
                'selectIdsQuery() must return an instance of %s', SelectQuery::class
            ));
        }

        if (!empty($columns)) {
            $selectIds->resetCols()->cols($columns);
        }

        $data = $this->db->fetchAssoc(
            $selectIds->getStatement(),
            ['ids' => $ids]
        );

        if (!$data) {
            throw new ResourceNotFoundException();
        }

        return new CollectionResult($data);
    }

    /**
     * @param int   $id
     * @param array $includes
     *
     * @return ItemResult
     */
    public function getOne($id, array $includes = [])
    {
        /** @var SelectQuery $selectOne */
        $selectOne = $this->selectOneQuery();
        if (!$selectOne) {
            throw new EndpointNotAvailableException();
        }
        if (!$selectOne instanceof SelectQuery) {
            throw new InvalidArgumentException(sprintf(
                'selectOneQuery() must return an instance of %s', SelectQuery::class
            ));
        }

        $data = $this->db->fetchRow($selectOne->getStatement(), ['id' => $id]);

        if (!$data) {
            throw new ResourceNotFoundException();
        }

        // Includes in Ergebnis integrieren
        $data = $this->integrateIncludes($includes, $data, false);

        return new ItemResult($data);
    }

    /**
     * Prüfen ob übergebene ID in Datenbank vorhanden ist
     *
     * @param int         $id
     * @param string|null $message Fehlermeldung wenn ID nicht vorhanden ist
     */
    public function checkOrFail($id, $message = null)
    {
        /** @var SelectQuery $selectOne */
        $select = $this->selectOneQuery();
        if (!$select) {
            throw new EndpointNotAvailableException();
        }
        if (!$select instanceof SelectQuery) {
            throw new InvalidArgumentException(sprintf(
                'selectOneQuery() must return an instance of %s', SelectQuery::class
            ));
        }

        $value = $this->db->fetchValue($select->getStatement(), ['id' => $id]);

        if ((int)$value !== (int)$id) {
            throw new ResourceNotFoundException($message === null ? 'Resource not found' : $message);
        }
    }

    /**
     * @param int         $id
     * @param array       $inputVars
     * @param array|null  $inputMapping Assoc-Array ['Eingabefeld' => 'Datenbankfeld']
     *
     * @return ItemResult
     */
    public function edit($id, $inputVars, $inputMapping = null)
    {
        $updateQuery = $this->updateQuery();
        if (!$updateQuery) {
            throw new EndpointNotAvailableException();
        }
        if (!$updateQuery instanceof UpdateQuery) {
            throw new InvalidArgumentException(sprintf(
                'updateQuery() must return an instance of %s', UpdateQuery::class
            ));
        }

        // Eingabe validieren
        $this->validateData($inputVars, $id);
        $inputVars['id'] = $id;

        // Eingabe- zu Datenbankfeld mappen
        $inputVars = $this->mapInputData($inputVars, $inputMapping);

        $bindValues = [];
        foreach ($inputVars as $inputKey => $inputVal) {
            $updateQuery->col($inputKey);
            $bindValues[$inputKey] = $inputVal;
        }

        $this->db->perform($updateQuery->getStatement(), $bindValues);

        // Bei Erfolg die geänderte Resource zurückliefern; mit Success-Flag
        $result = $this->getOne($id);
        $result->setSuccess(true);

        return $result;
    }

    /**
     * @param array      $inputVars
     * @param array|null $inputMapping Assoc-Array ['Eingabefeld' => 'Datenbankfeld']
     *
     * @return ItemResult
     */
    public function insert($inputVars, $inputMapping = null)
    {
        $insertQuery = $this->insertQuery();
        if (!$insertQuery) {
            throw new EndpointNotAvailableException();
        }
        if (!$insertQuery instanceof InsertQuery) {
            throw new InvalidArgumentException(sprintf(
                'insertQuery() must return an instance of %s', InsertQuery::class
            ));
        }

        // Eingabe validieren
        $this->validateData($inputVars);

        // Eingabe- zu Datenbankfeld mappen
        $inputVars = $this->mapInputData($inputVars, $inputMapping);

        $bindValues = [];
        foreach ($inputVars as $inputKey => $inputVal) {
            $insertQuery->col($inputKey);
            $bindValues[$inputKey] = $inputVal;
        }

        $this->db->perform($insertQuery->getStatement(), $bindValues);
        $id = $this->db->lastInsertId();

        // Bei Erfolg die angelegte Resource zurückliefern; mit Success-Flag
        $result = $this->getOne($id);
        $result->setSuccess(true);

        return $result;
    }

    /**
     * @param int $id
     *
     * @return ItemResult
     */
    public function delete($id)
    {
        $deleteQuery = $this->deleteQuery();
        if (!$deleteQuery) {
            throw new EndpointNotAvailableException();
        }
        if (!$deleteQuery instanceof DeleteQuery && !$deleteQuery instanceof UpdateQuery) {
            throw new InvalidArgumentException(sprintf(
                'deleteQuery() must return an instance of %s or %s', DeleteQuery::class, UpdateQuery::class
            ));
        }

        try {
            $this->db->perform($deleteQuery->getStatement(), ['id' => $id]);
            $success = true;
        } catch (Exception $e) {
            $success = false;
        }

        $result = new ItemResult(['id' => $id]);
        $result->setSuccess($success);

        return $result;
    }

    /**
     * Eingabe- zu Datenbankfeld mappen
     *
     * @param array      $inputVars
     * @param array|null $inputMapping Assoc-Array ['Eingabefeld' => 'Datenbankfeld']
     *
     * @return array
     */
    protected function mapInputData($inputVars, $inputMapping = null)
    {
        if (empty($inputMapping)) {
            return $inputVars;
        }

        foreach ($inputMapping as $inputKey => $dbKey) {
            if (empty($inputKey) || empty($dbKey)) {
                continue;
            }
            if ($inputKey === $dbKey) {
                continue;
            }
            if (array_key_exists($inputKey, $inputVars)) {
                $inputVars[$dbKey] = $inputVars[$inputKey];
                unset($inputVars[$inputKey]);
            }
        }

        return $inputVars;
    }

    /**
     * @param int $itemsTotal
     * @param int $itemsCurrent
     * @param int $itemsPerPage
     * @param int $pageCurrent
     *
     * @return array
     */
    protected function getPagination($itemsTotal, $itemsCurrent, $itemsPerPage, $pageCurrent)
    {
        return [
            'items_per_page' => (int)$itemsPerPage,
            'items_current'  => (int)$itemsCurrent,
            'items_total'    => (int)$itemsTotal,
            'page_current'   => (int)$pageCurrent,
            'page_last'      => (int)ceil($itemsTotal / $itemsPerPage),
        ];
    }

    /**
     * @param string $resourceClass
     *
     * @return AbstractResource
     */
    protected function getResource($resourceClass)
    {
        return new $resourceClass(
            $this->db,
            $this->validator
        );
    }
}
