<?php

namespace Xentral\Components\Database\Adapter;

use Generator;
use mysqli;
use mysqli_result;
use mysqli_stmt;
use Xentral\Components\Database\DatabaseConfig;
use Xentral\Components\Database\Exception\BindParameterException;
use Xentral\Components\Database\Exception\ConnectionException;
use Xentral\Components\Database\Exception\EscapingException;
use Xentral\Components\Database\Exception\QueryFailureException;
use Xentral\Components\Database\Exception\TransactionException;
use Xentral\Components\Database\Parser\MysqliArrayValueParser;
use Xentral\Components\Database\Parser\MysqliNamedParameterParser;
use Xentral\Components\Database\Profiler\ProfilerInterface;

final class MysqliAdapter implements AdapterInterface
{
    /** @var mysqli|null $connection */
    private $connection;

    /** @var DatabaseConfig $config */
    private $config;

    /** @var bool $transactionActive */
    private $transactionActive = false;

    /** @var int|null $reconnectCounter */
    private $reconnectCounter;

    /** @var int $reconnectLimit */
    private $reconnectMaxCount = 5;

    /** @var ProfilerInterface|null $profiler */
    private $profiler;

    /**
     * @param DatabaseConfig         $config
     * @param ProfilerInterface|null $profiler
     */
    public function __construct(DatabaseConfig $config, ProfilerInterface $profiler = null)
    {
        $this->config = $config;
        $this->profiler = $profiler;
    }

    /**
     * @throws ConnectionException
     *
     * @return void
     */
    public function connect()
    {
        if ($this->connection !== null) {
            return;
        }

        if ($this->reconnectCounter === null) {
            $this->reconnectCounter = 0;
        } else {
            $this->reconnectCounter++;
        }

        if ($this->reconnectCounter >= $this->reconnectMaxCount) {
            throw new ConnectionException(sprintf(
                'Too many reconnects. Reconnect count: %d (Max allowed %d)',
                $this->reconnectCounter,
                $this->reconnectMaxCount
            ));
        }

        $this->startProfiler(__FUNCTION__);

        $connection = new mysqli(
            $this->config->getHostname(),
            $this->config->getUsername(),
            $this->config->getPassword(),
            null,
            $this->config->getPort()
        );

        if ($connection->connect_errno > 0) {
            throw new ConnectionException(sprintf(
                'Database connection to host "%s" failed. Error code #%s. Error message: %s',
                $this->config->getHostname(),
                $connection->connect_errno,
                $connection->connect_error
            ));
        }

        $connection->select_db($this->config->getDatabase());
        if ($connection->errno > 0) {
            throw new ConnectionException(sprintf(
                'Database selection failed for database "%s". Error code #%s. Error message: %s',
                $this->config->getDatabase(),
                $connection->errno,
                $connection->error
            ));
        }

        // @see https://www.php.net/manual/de/mysqlinfo.concepts.charset.php
        if (!$connection->set_charset($this->config->getCharset())) {
            throw new ConnectionException(sprintf(
                'Failed to set character set "%s". Error: %s',
                $this->config->getCharset(),
                $connection->error
            ));
        }

        if (!$connection->autocommit(true)) {
            throw new ConnectionException(sprintf(
                'Failed to activate auto commit. Error: %s',
                $connection->error
            ));
        }

        $this->finishProfiler(null, [
            'dbname' => $this->config->getDatabase(),
            'host'   => $this->config->getHostname(),
            'port'   => $this->config->getPort(),
        ]);

        $this->connection = $connection;

        foreach ($this->config->getQueries() as $query) {
            $this->perform($query);
        }
    }

    /**
     * @return void
     */
    public function disconnect()
    {
        if ($this->connection === null) {
            return;
        }

        $this->startProfiler(__FUNCTION__);
        $this->connection->close();
        $this->connection = null;
        $this->finishProfiler();
    }

    /**
     * @return bool
     */
    public function inTransaction()
    {
        return $this->transactionActive;
    }

    /**
     * @throws TransactionException If transaction is already started
     *
     * @return void
     */
    public function beginTransaction()
    {
        if ($this->inTransaction()) {
            throw new TransactionException('Transaction is already started.');
        }

        $this->connect();

        if ($this->connection->begin_transaction() === false) {
            throw new TransactionException(sprintf('Transaction start failed: %s', $this->connection->error));
        }
        $this->transactionActive = true;
    }

    /**
     * @throws TransactionException
     *
     * @return void
     */
    public function commit()
    {
        if (!$this->inTransaction()) {
            throw new TransactionException('Transaction not started.');
        }

        $this->connection->commit();
        $this->connection->autocommit(true);
        $this->transactionActive = false;
    }

    /**
     * @throws TransactionException
     *
     * @return void
     */
    public function rollback()
    {
        if (!$this->inTransaction()) {
            throw new TransactionException('Transaction not started.');
        }

        $this->connection->rollback();
        $this->connection->autocommit(true);
        $this->transactionActive = false;
    }

    /**
     * @return int
     */
    public function lastInsertId()
    {
        return (int)$this->connection->insert_id;
    }

    /**
     * @param array  $values
     * @param string $statement
     *
     * @throws QueryFailureException
     *
     * @return void
     */
    public function perform($statement, array $values = [])
    {
        $this->connect();

        $this->startProfiler(__FUNCTION__);
        $query = $this->getMysqliStatement($statement, $values);
        if (!is_object($query)) {
            throw new QueryFailureException(sprintf('Database query failed: %s', $this->connection->error));
        }

        $query->close();
        $this->finishProfiler($statement, $values);
    }

    /**
     * @param string $statement
     * @param array  $values
     *
     * @throws QueryFailureException
     *
     * @return int
     */
    public function fetchAffected($statement, array $values = [])
    {
        $this->connect();

        $this->startProfiler(__FUNCTION__);
        $query = $this->getMysqliStatement($statement, $values);
        if (!is_object($query)) {
            throw new QueryFailureException(sprintf('Database query failed: %s', $this->connection->error));
        }

        $affectedRows = (int)$query->affected_rows;
        $query->close();
        $this->finishProfiler($statement, $values);

        return $affectedRows;
    }

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return array
     */
    public function fetchAll($statement, array $values = [])
    {
        $this->connect();

        $this->startProfiler(__FUNCTION__);
        $result = $this->getMysqliResult($statement, $values);

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        $result->close();
        $this->finishProfiler($statement, $values);

        return $data;
    }

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return array
     */
    public function fetchAssoc($statement, array $values = [])
    {
        $this->connect();

        $this->startProfiler(__FUNCTION__);
        $result = $this->getMysqliResult($statement, $values);

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $assocKey = reset($row); // Fetch first array value
            $data[$assocKey] = $row;
        }

        $result->close();
        $this->finishProfiler($statement, $values);

        return $data;
    }

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return array Empty array on empty result
     */
    public function fetchRow($statement, array $values = [])
    {
        $this->connect();

        $this->startProfiler(__FUNCTION__);
        $result = $this->getMysqliResult($statement, $values);
        if ($result->num_rows === 0) {
            $result->close();

            return [];
        }

        $data = $result->fetch_assoc();
        $result->close();
        $this->finishProfiler($statement, $values);

        return $data;
    }

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return int|float|string|false false on empty result
     */
    public function fetchValue($statement, array $values = [])
    {
        $this->connect();

        $this->startProfiler(__FUNCTION__);
        $result = $this->getMysqliResult($statement, $values);
        if ($result->num_rows === 0) {
            $result->close();

            return false;
        }

        $data = $result->fetch_assoc();
        $result->close();
        $this->finishProfiler($statement, $values);

        return reset($data);
    }

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return array
     */
    public function fetchCol($statement, array $values = [])
    {
        $this->connect();

        $this->startProfiler(__FUNCTION__);
        $result = $this->getMysqliResult($statement, $values);

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $firstValue = reset($row); // Fetch first array value
            $data[] = $firstValue;
        }

        $result->close();
        $this->finishProfiler($statement, $values);

        return $data;
    }

    /**
     * @param string $statement
     * @param array  $values
     *
     * @throws QueryFailureException
     *
     * @return array
     */
    public function fetchPairs($statement, array $values = [])
    {
        $this->connect();

        $this->startProfiler(__FUNCTION__);
        $result = $this->getMysqliResult($statement, $values);
        if ($result->field_count !== 2) {
            throw new QueryFailureException('Field count does not match. fetchPairs() allows only two fields.');
        }

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $key = array_shift($row);
            $value = array_shift($row);
            $data[$key] = $value;
        }

        $result->close();
        $this->finishProfiler($statement, $values);

        return $data;
    }

    /**
     * @param string $statement
     * @param array  $values
     * @param bool   $includeGroupColumn
     *
     * @return array
     */
    public function fetchGroup($statement, array $values = [], $includeGroupColumn = false)
    {
        $this->connect();

        $this->startProfiler(__FUNCTION__);
        $result = $this->getMysqliResult($statement, $values);

        $data = [];
        $includeGroupColumn = (bool)$includeGroupColumn;
        while ($row = $result->fetch_assoc()) {
            $group = $includeGroupColumn === true ? reset($row) : array_shift($row); // Fetch first array value
            if (!isset($data[$group])) {
                $data[$group] = [];
            }
            $data[$group][] = $row;
        }

        $result->close();
        $this->finishProfiler($statement, $values);

        return $data;
    }

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return Generator
     */
    public function yieldAll($statement, array $values = [])
    {
        $this->connect();

        $this->startProfiler(__FUNCTION__);
        $result = $this->getMysqliResult($statement, $values);
        $this->finishProfiler($statement, $values);

        while ($row = $result->fetch_assoc()) {
            yield $row;
        }

        $result->close();
    }

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return Generator
     */
    public function yieldAssoc($statement, array $values = [])
    {
        $this->connect();

        $this->startProfiler(__FUNCTION__);
        $result = $this->getMysqliResult($statement, $values);
        $this->finishProfiler($statement, $values);

        while ($row = $result->fetch_assoc()) {
            $assocKey = reset($row); // Fetch first array value

            yield $assocKey => $row;
        }

        $result->close();
    }

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return Generator
     */
    public function yieldCol($statement, array $values = [])
    {
        $this->connect();

        $this->startProfiler(__FUNCTION__);
        $result = $this->getMysqliResult($statement, $values);
        $this->finishProfiler($statement, $values);

        while ($row = $result->fetch_assoc()) {
            $firstValue = reset($row); // Fetch first array value

            yield $firstValue;
        }

        $result->close();
    }

    /**
     * @param string $statement
     * @param array  $values
     *
     * @throws QueryFailureException
     *
     * @return Generator
     */
    public function yieldPairs($statement, array $values = [])
    {
        $this->connect();

        $this->startProfiler(__FUNCTION__);
        $result = $this->getMysqliResult($statement, $values);
        $this->finishProfiler($statement, $values);

        if ($result->field_count !== 2) {
            throw new QueryFailureException('Field count does not match. yieldPairs() allows only two fields.');
        }

        while ($row = $result->fetch_assoc()) {
            $key = array_shift($row);
            $value = array_shift($row);

            yield $key => $value;
        }

        $result->close();
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
        if ($isNullable === true && $value === null) {
            return 'NULL';
        }

        if (!is_bool($value)) {
            throw new EscapingException('Can not escape bool. Value is not a bool.');
        }

        return $value === true ? '1' : '0';
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
        if ($isNullable === true && $value === null) {
            return 'NULL';
        }

        if (!is_int($value)) {
            throw new EscapingException('Can not escape integer. Value is not an integer.');
        }

        return (string)(int)$value;
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
        if ($isNullable === true && $value === null) {
            return 'NULL';
        }

        if (!is_numeric($value)) {
            throw new EscapingException('Can not escape decimal. Value is not numeric.');
        }

        return (string)$value;
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
        if ($isNullable === true && $value === null) {
            return 'NULL';
        }
        if (!is_string($value)) {
            throw new EscapingException('Can not escape string. Value is not a string.');
        }

        $this->connect();

        return "'" . $this->connection->real_escape_string($value) . "'";
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
        if (!is_string($value)) {
            throw new EscapingException('Can not escape identifier. Passed value is not a string.');
        }
        if (empty(trim($value))) {
            throw new EscapingException('Can not escape identifier. Passed value is empty.');
        }

        $parts = explode('.', $value);
        if (count($parts) > 2) {
            throw new EscapingException('Can not escape identifier. Identifier contains more than one dots.');
        }

        $partsCleaned = [];
        foreach ($parts as $part) {
            if (empty(trim($part))) {
                throw new EscapingException(
                    'Can not escape identifier. Parts before and after the dot can not be empty.'
                );
            }
            if (strlen($part) > 64) {
                throw new EscapingException(
                    'Can not escape identifier. Identifier is too long. Only 64 characters are allowed.'
                );
            }
            $partCleaned = preg_replace('/[^A-Za-z0-9_]+/', '', $part);
            if (strlen($partCleaned) !== strlen($part)) {
                throw new EscapingException(
                    'Can not escape identifier. Passed value contains invalid characters. ' .
                    'Valid characters: A-Z, a-z, 0-9, Underscore'
                );
            }

            $partsCleaned[] = '`' . $partCleaned . '`';
        }

        return implode('.', $partsCleaned);
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $this->connection = null;
        $this->transactionActive = false;
        $this->config = clone $this->config;
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * @return void
     */
    public function __wakeup()
    {
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return [];
    }

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return mysqli_stmt
     */
    private function getMysqliStatement($statement, array $values = [])
    {
        list($statement, $values) = $this->replaceArrayValues($statement, $values);
        list($rebuildStatement, $bindValues, $parameterNames) = $this->replaceNamedParameters($statement, $values);

        $query = $this->connection->prepare($rebuildStatement);
        if ($query === false && $this->connection->errno === 2006) {
            // Code 2006 = MySQL server has gone away
            // Falls Verbindung in einen Timeout gelaufen ist
            // => Verbindung wiederherstellen und Prepare erneut probieren
            $this->disconnect();
            $this->connect();
            $query = $this->connection->prepare($rebuildStatement);
        }
        if ($query === false || !is_object($query)) {
            throw new QueryFailureException(
                sprintf(
                    'Database prepare failed. Error code #%s. Error message: %s',
                    $this->connection->errno,
                    $this->connection->error
                ),
                (int)$this->connection->errno
            );
        }

        $this->bindParametersToMysqliStatement($query, $bindValues, $parameterNames);
        $query->execute();

        if ($query->errno > 0) {
            throw new QueryFailureException(
                sprintf('Database query failed. Error code #%s. Error message: %s', $query->errno, $query->error),
                (int)$query->errno
            );
        }

        return $query;
    }

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return array
     */
    private function replaceArrayValues($statement, array $values = [])
    {
        $parser = new MysqliArrayValueParser();
        $result = $parser->rebuild($statement, $values);

        return [$result['statement'], $result['values']];
    }

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return array
     */
    private function replaceNamedParameters($statement, array $values = [])
    {
        $parser = new MysqliNamedParameterParser();
        $result = $parser->rebuild($statement, $values);

        return [$result['statement'], $result['values'], $result['params']];
    }

    /**
     * @param mysqli_stmt $statement
     * @param array       $bindValues     Values for binding
     * @param array       $parameterNames Original parameter names (for debugging only)
     *
     * @return void
     */
    private function bindParametersToMysqliStatement($statement, $bindValues, $parameterNames)
    {
        if (empty($bindValues)) {
            return;
        }

        $bindTypes = '';
        foreach ($bindValues as $index => &$bindValue) {
            if (is_bool($bindValue)) {
                $bindValue = (int)$bindValue;
                $bindTypes .= 'i'; // integer
                continue;
            }
            if (is_float($bindValue)) {
                $bindTypes .= 'd'; // double
                continue;
            }
            if (is_array($bindValue)) {
                throw new BindParameterException(sprintf(
                    'Can not bind parameter of type "array" to placeholder "%s".',
                    $parameterNames[$index]
                ));
            }
            if (is_object($bindValue)) {
                throw new BindParameterException(sprintf(
                    'Can not bind parameter of type "object" to placeholder "%s".',
                    $parameterNames[$index]
                ));
            }
            $bindTypes .= 's'; // string
        }
        unset($bindValue);

        $statement->bind_param($bindTypes, ...$bindValues);
    }

    /**
     * @param       $statement
     * @param array $values
     *
     * @return mysqli_result
     */
    private function getMysqliResult($statement, array $values = [])
    {
        $query = $this->getMysqliStatement($statement, $values);
        $result = $query->get_result();
        $query->close();

        if ($result === false) {
            throw new QueryFailureException(
                sprintf('Database query failed. Error code #%s. Error message: %s', $query->errno, $query->error),
                (int)$query->errno
            );
        }

        return $result;
    }

    /**
     * @param string $methodName
     *
     * @return void
     */
    private function startProfiler($methodName)
    {
        if ($this->profiler === null) {
            return;
        }

        $this->profiler->start(__CLASS__, $methodName);
    }

    /**
     * @param string|null $statement
     * @param array       $values
     *
     * @return void
     */
    private function finishProfiler($statement = null, array $values = [])
    {
        if ($this->profiler === null) {
            return;
        }

        $this->profiler->finish($statement, $values);
    }
}
