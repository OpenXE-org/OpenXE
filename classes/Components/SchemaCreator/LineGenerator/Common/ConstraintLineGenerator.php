<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\LineGenerator\Common;

use Xentral\Components\Database\Database;
use Xentral\Components\Database\Exception\QueryFailureException;
use Xentral\Components\SchemaCreator\Exception\LineGeneratorException;

final class ConstraintLineGenerator
{
    /** @var Database $db */
    private $db;

    /**
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $tableName
     *
     * @throws LineGeneratorException
     *
     * @return array
     */
    public function fetchConstraintsFromDb(string $tableName): array
    {
        try {
            $dbName = $this->db->fetchValue('SELECT DATABASE()');
            $constraints = $this->db->fetchAll(
                sprintf(
                    "SELECT `COLUMN_NAME`, `REFERENCED_TABLE_NAME`, 
                    `REFERENCED_COLUMN_NAME`, `CONSTRAINT_NAME` 
                    FROM `information_schema`.`key_column_usage` 
                    WHERE `referenced_table_name` IS NOT NULL AND `TABLE_SCHEMA` = '%s' AND `TABLE_NAME` = '%s' ",
                    $dbName,
                    $tableName

                )
            );
        } catch (QueryFailureException $exception) {
            throw new LineGeneratorException(
                $exception->getMessage(), $exception->getCode(), $exception->getPrevious()
            );
        }
        $result = [];

        foreach ($constraints as $constraint) {
            $constraintName = $constraint['CONSTRAINT_NAME'];
            $resultKey = array_search($constraintName, array_column($result, 'name'), true);
            if ($resultKey !== false) {
                $result[$resultKey]['columns'][] = $constraint['COLUMN_NAME'];
                $result[$resultKey]['reference_columns'][] = $constraint['COLUMN_NAME'];
                continue;
            }

            $result[] = [
                'name'              => $constraint['CONSTRAINT_NAME'],
                'reference_table'   => $constraint['REFERENCED_TABLE_NAME'],
                'reference_columns' => [$constraint['REFERENCED_COLUMN_NAME']],
                'columns'           => [$constraint['COLUMN_NAME']],
            ];
        }

        return $result;
    }
}
