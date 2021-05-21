<?php

namespace Xentral\Components\Database\Adapter;

use Generator;

interface AdapterInterface
{
    /**
     * @return void
     */
    public function connect();

    /**
     * @return void
     */
    public function disconnect();

    /**
     * @return bool
     */
    public function inTransaction();

    /**
     * @return void
     */
    public function beginTransaction();

    /**
     * @return void
     */
    public function rollback();

    /**
     * @return void
     */
    public function commit();

    /**
     * @return int
     */
    public function lastInsertId();

    /**
     * @param array  $values
     * @param string $statement
     *
     * @return void
     */
    public function perform($statement, array $values = []);

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return int
     */
    public function fetchAffected($statement, array $values = []);

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return array
     */
    public function fetchAll($statement, array $values = []);

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return array
     */
    public function fetchAssoc($statement, array $values = []);

    /**
     * @param string $statement
     * @param array  $values
     * @param bool   $includeGroupColumn
     *
     * @return array
     */
    public function fetchGroup($statement, array $values = [], $includeGroupColumn = false);

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return int|float|string|false false on empty result
     */
    public function fetchValue($statement, array $values = []);

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return array
     */
    public function fetchRow($statement, array $values = []);

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return array
     */
    public function fetchCol($statement, array $values = []);

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return array
     */
    public function fetchPairs($statement, array $values = []);

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return Generator
     */
    public function yieldCol($statement, array $values = []);

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return Generator
     */
    public function yieldAssoc($statement, array $values = []);

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return Generator
     */
    public function yieldAll($statement, array $values = []);

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return Generator
     */
    public function yieldPairs($statement, array $values = []);

    /**
     * Escapes values for "BOOLEAN" and (TINY)INT columns
     *
     * @param bool|null $value
     * @param bool      $isNullable
     *
     * @return string
     */
    public function escapeBool($value, $isNullable = false);

    /**
     * Escapes values for INT columns
     *
     * @param mixed $value
     * @param bool  $isNullable
     *
     * @return string
     */
    public function escapeInt($value, $isNullable = false);

    /**
     * Escapes values for FLOAT, DOUBLE and REAL columns
     *
     * @param mixed $value
     * @param bool  $isNullable
     *
     * @return string
     */
    public function escapeDecimal($value, $isNullable = false);

    /**
     * Escapes values for CHAR, VARCHAR, TEXT and BLOB columns
     *
     * @param mixed $value
     * @param bool  $isNullable
     *
     * @return string
     */
    public function escapeString($value, $isNullable = false);

    /**
     * Escapes and quotes an identifier (column or table name)
     *
     * @param string $value
     *
     * @return string
     */
    public function escapeIdentifier($value);
}
