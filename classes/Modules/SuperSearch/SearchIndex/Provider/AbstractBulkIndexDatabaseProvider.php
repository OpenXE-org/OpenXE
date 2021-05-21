<?php

namespace Xentral\Modules\SuperSearch\SearchIndex\Provider;

use Closure;
use DateTimeInterface;
use Exception;
use Xentral\Components\Database\Database;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Modules\SuperSearch\Exception\FormatterFailureException;
use Xentral\Modules\SuperSearch\Exception\InvalidReturnTypeException;
use Xentral\Modules\SuperSearch\Exception\SuperSearchExceptionInterface;
use Xentral\Modules\SuperSearch\SearchIndex\Collection\ItemFormatterCollection;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexIdentifier;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexItem;

abstract class AbstractBulkIndexDatabaseProvider implements
    SearchIndexProviderInterface,
    BulkIndexProviderInterface,
    ItemIndexProviderInterface,
    DiffIndexProviderInterface
{
    /** @var Database $db */
    protected $db;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * @param SelectQuery $query
     *
     * @return void
     */
    abstract protected function configureBaseQuery(SelectQuery $query);

    /**
     * @param SelectQuery $baseQuery
     * @param int|string  $indexId
     *
     * @return void
     */
    abstract protected function configureItemQuery(SelectQuery $baseQuery, $indexId);

    /**
     * @param SelectQuery       $baseQuery
     * @param DateTimeInterface $since
     *
     * @return void
     */
    abstract protected function configureSinceQuery(SelectQuery $baseQuery, DateTimeInterface $since);

    /**
     * @param SelectQuery $baseQuery
     *
     * @return void
     */
    abstract protected function configureCountQuery(SelectQuery $baseQuery);

    /**
     * @return Closure
     */
    abstract protected function getRowFormatter();

    /**
     * @return int Anzahl der IndexItems die in einem Durchlauf geschrieben werden
     */
    public function getBulkSize()
    {
        return 20000;
    }

    /**
     * @param IndexIdentifier $identifier
     *
     * @throws InvalidReturnTypeException
     *
     * @return IndexItem|null
     */
    public function getItem(IndexIdentifier $identifier)
    {
        $select = $this->db->select();
        $this->configureBaseQuery($select);
        $this->configureItemQuery($select, $identifier->getId());

        $row = $this->db->fetchRow(
            $select->getStatement(),
            $select->getBindValues()
        );

        if (empty($row)) {
            return null;
        }

        try {
            // Formatter aufrufen
            $formatter = $this->getRowFormatter();
            $item = $formatter($row);
        } catch (SuperSearchExceptionInterface $exception) {
            throw new FormatterFailureException(
                sprintf('Formatter failed. Row data: %s', var_export($row, true)),
                $exception->getCode(),
                $exception
            );
        }

        // Prüfen ob Formatter den richtigen Typ zurückliefert
        if (!$item instanceof IndexItem) {
            $itemType = gettype($item);
            if ($itemType === 'object') {
                $itemType = get_class($item);
            }
            throw new InvalidReturnTypeException(sprintf(
                '"%s::getRowFormatter()" returned invalid type. Required type "%s". Returned type "%s".',
                get_class($this),
                IndexItem::class,
                $itemType
            ));
        }

        return $item;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getItemsSince(DateTimeInterface $since)
    {
        $select = $this->db->select();
        $this->configureBaseQuery($select);
        $this->configureSinceQuery($select, $since);

        $callback = $this->getRowFormatter();
        $data = $this->db->fetchAll(
            $select->getStatement(),
            $select->getBindValues()
        );

        return new ItemFormatterCollection($data, $callback);
    }

    /**
     * @inheritDoc
     */
    public function getTotalCount()
    {
        $select = $this->db->select();
        $this->configureBaseQuery($select);
        $select->resetCols();
        $this->configureCountQuery($select);

        return (int)$this->db->fetchValue(
            $select->getStatement(),
            $select->getBindValues()
        );
    }

    /**
     * @param int $offset
     * @param int $count
     *
     * @throws InvalidReturnTypeException
     * @throws Exception
     *
     * @return ItemFormatterCollection
     */
    public function getBulkItems($offset, $count)
    {
        $select = $this->db->select();
        $this->configureBaseQuery($select);

        $select->offset($offset);
        $select->limit($count);

        $callback = $this->getRowFormatter();
        $data = $this->db->fetchAll(
            $select->getStatement(),
            $select->getBindValues()
        );

        return new ItemFormatterCollection($data, $callback);
    }
}
