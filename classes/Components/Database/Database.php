<?php /** @noinspection PhpInconsistentReturnPointsInspection */

namespace Xentral\Components\Database;

use Generator;
use Xentral\Components\Database\Adapter\AdapterInterface;
use Xentral\Components\Database\Exception\EscapingException;
use Xentral\Components\Database\Exception\TransactionException;
use Xentral\Components\Database\SqlQuery\DeleteQuery;
use Xentral\Components\Database\SqlQuery\InsertQuery;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Components\Database\SqlQuery\QueryFactory;
use Xentral\Components\Database\SqlQuery\UpdateQuery;

final class Database
{
    /** @var AdapterInterface $adapter */
    private $adapter;

    /** @var QueryFactory $queryFactory */
    private $queryFactory;

    /**
     * @param AdapterInterface $adapter
     * @param QueryFactory     $queryFactory
     */
    public function __construct(AdapterInterface $adapter, QueryFactory $queryFactory)
    {
        $this->adapter = $adapter;
        $this->queryFactory = $queryFactory;
    }

    /**
     * Aura.SqlQuery
     */

    /**
     * @return SelectQuery
     */
    public function select()
    {
        return $this->queryFactory->newSelect();
    }

    /**
     * @return InsertQuery
     */
    public function insert()
    {
        return $this->queryFactory->newInsert();
    }

    /**
     * @return UpdateQuery
     */
    public function update()
    {
        return $this->queryFactory->newUpdate();
    }

    /**
     * @return DeleteQuery
     */
    public function delete()
    {
        return $this->queryFactory->newDelete();
    }

    /**
     * ENDE: Aura.SqlQuery
     */

    /**
     * Close database connection
     *
     * @return void
     */
    public function close()
    {
        $this->adapter->disconnect();
    }

    /**
     * Executes simple queries without named parameters
     *
     * Use self::perform() for queries with named parameters.
     *
     * @param string $query
     *
     * @return void
     */
    public function exec($query)
    {
        $this->adapter->perform($query, []);
    }

    /**
     * @return int
     */
    public function lastInsertId()
    {
        return (int)$this->adapter->lastInsertId();
    }

    /**
     * @throws TransactionException If transaction is already started
     *
     * @return void
     */
    public function beginTransaction()
    {
        $this->adapter->beginTransaction();
    }

    /**
     * @return void
     */
    public function commit()
    {
        $this->adapter->commit();
    }

    /**
     * @return void
     */
    public function rollBack()
    {
        $this->adapter->rollBack();
    }

    /**
     * @return bool
     */
    public function inTransaction()
    {
        return $this->adapter->inTransaction();
    }

    /**
     * @param string $query
     * @param array  $values
     *
     * @return array Empty array on empty result
     */
    public function fetchAll($query, array $values = [])
    {
        return $this->adapter->fetchAll($query, $values);
    }

    /**
     * @param string $query
     * @param array  $values
     *
     * @return array Empty array on empty result
     */
    public function fetchAssoc($query, array $values = [])
    {
        return $this->adapter->fetchAssoc($query, $values);
    }

    /**
     * @param string $query
     * @param array  $values
     * @param bool   $includeGroupColumn
     *
     * @return array Empty array on empty result
     */
    public function fetchGroup($query, array $values = [], $includeGroupColumn = false)
    {
        return $this->adapter->fetchGroup($query, $values, (bool)$includeGroupColumn);
    }

    /**
     * @param string $query
     * @param array  $values
     *
     * @return array Empty array on empty result
     */
    public function fetchRow($query, array $values = [])
    {
        return $this->adapter->fetchRow($query, $values);
    }

    /**
     * @param string $query
     * @param array  $values
     *
     * @return array Empty array on empty result
     */
    public function fetchPairs($query, array $values = [])
    {
        return $this->adapter->fetchPairs($query, $values);
    }

    /**
     * @param string $query
     * @param array  $values
     *
     * @return int|float|string|false false on empty result
     */
    public function fetchValue($query, array $values = [])
    {
        return $this->adapter->fetchValue($query, $values);
    }

    /**
     * @param string $query
     * @param array  $values
     *
     * @return int
     */
    public function fetchAffected($query, array $values = [])
    {
        return $this->adapter->fetchAffected($query, $values);
    }

    /**
     * @param string $query
     * @param array  $values
     *
     * @return array Empty array on empty result
     */
    public function fetchCol($query, array $values = [])
    {
        return $this->adapter->fetchCol($query, $values);
    }

    /**
     * @param string $query
     * @param array  $values
     *
     * @return Generator
     */
    public function yieldAll($query, array $values = [])
    {
        return $this->adapter->yieldAll($query, $values);
    }

    /**
     * @param string $query
     * @param array  $values
     *
     * @return Generator
     */
    public function yieldAssoc($query, array $values = [])
    {
        return $this->adapter->yieldAssoc($query, $values);
    }

    /**
     * @param string $query
     * @param array  $values
     *
     * @return Generator
     */
    public function yieldPairs($query, array $values = [])
    {
        return $this->adapter->yieldPairs($query, $values);
    }

    /**
     * @param string $query
     * @param array  $values
     *
     * @return Generator
     */
    public function yieldCol($query, array $values = [])
    {
        return $this->adapter->yieldCol($query, $values);
    }

    /**
     * @param string $query
     * @param array  $values
     *
     * @return void
     */
    public function perform($query, array $values = [])
    {
        $this->adapter->perform($query, $values);
    }

    /**
     * Escapes values for "BOOLEAN" and (TINY)INT columns
     *
     * @param bool|null $value
     * @param bool      $isNullable
     *
     * @throws EscapingException
     *
     * @return string
     */
    public function escapeBool($value, $isNullable = false)
    {
        return $this->adapter->escapeBool($value, $isNullable);
    }

    /**
     * Escapes values for INT columns
     *
     * @param mixed $value
     * @param bool  $isNullable
     *
     * @throws EscapingException
     *
     * @return string
     */
    public function escapeInt($value, $isNullable = false)
    {
        return $this->adapter->escapeInt($value, $isNullable);
    }

    /**
     * Escapes values for FLOAT, DOUBLE and REAL columns
     *
     * @param mixed $value
     * @param bool  $isNullable
     *
     * @throws EscapingException
     *
     * @return string
     */
    public function escapeDecimal($value, $isNullable = false)
    {
        return $this->adapter->escapeDecimal($value, $isNullable);
    }

    /**
     * Escapes values for CHAR, VARCHAR, TEXT and BLOB columns
     *
     * @param mixed $value
     * @param bool  $isNullable
     *
     * @throws EscapingException
     *
     * @return string
     */
    public function escapeString($value, $isNullable = false)
    {
        return $this->adapter->escapeString($value, $isNullable);
    }

    /**
     * Escapes and quotes an identifier (column or table name)
     *
     * @param string $value
     *
     * @throws EscapingException
     *
     * @return string
     */
    public function escapeIdentifier($value)
    {
        return $this->adapter->escapeIdentifier($value);
    }
}
