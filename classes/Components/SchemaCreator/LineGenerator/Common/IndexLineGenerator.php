<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\LineGenerator\Common;

use Xentral\Components\Database\Database;
use Xentral\Components\Database\Exception\EscapingException;
use Xentral\Components\Database\Exception\QueryFailureException;
use Xentral\Components\SchemaCreator\Exception\LineGeneratorException;
use Xentral\Components\SchemaCreator\Index\Constraint;
use Xentral\Components\SchemaCreator\Interfaces\PrimaryKeyInterface;
use Xentral\Components\SchemaCreator\Interfaces\IndexInterface;

final class IndexLineGenerator
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
     * @param string $indexName
     *
     * @throws EscapingException
     *
     * @return string
     */
    private function generateName(string $indexName): string
    {
        if (!empty($indexName)) {
            $indexName = $this->escape($indexName);
        }

        return $indexName;
    }

    /**
     * @param $creatorIndex
     *
     * @throws EscapingException
     *
     * @return string
     */
    private function buildReferences($creatorIndex): string
    {
        if ($creatorIndex instanceof Constraint) {
            $default = ['delete'];
            $asCascade = $creatorIndex->getCascadeOn();
            if (empty($asCascade)) {
                $asCascade = $default;
            }
            $cascade = '';
            foreach ($asCascade as $cascadeCase) {
                $cascade .= sprintf(' ON %s CASCADE ', strtoupper($cascadeCase));
            }

            return sprintf(
                'FOREIGN KEY (%s) REFERENCES %s (%s)%s',
                implode(',', array_map([$this, 'escape'],$creatorIndex->getForeignKey())),
                $this->escape($creatorIndex->getParentTable()),
                implode(',', array_map([$this, 'escape'], $creatorIndex->getParenId())),
                $cascade
            );
        }

        $reference = implode(
            ',',
            array_map(
                function ($reference) {
                    return $this->escape($reference);
                },
                $creatorIndex->getReferences()
            )
        );

        return sprintf('(%s)', $reference);
    }

    /**
     * @param IndexInterface $creatorIndex
     *
     * @throws EscapingException
     *
     * @return string
     */
    public function generateLine(IndexInterface $creatorIndex): string
    {
        $line = $creatorIndex->getType();

        if (!($creatorIndex instanceof PrimaryKeyInterface)) {
            $lineName = $this->generateName($creatorIndex->getName());
            $line .= ' ' . $lineName;
        }

        $line .= ' ' . $this->buildReferences($creatorIndex);

        return trim($line);
    }

    /**
     * @param IndexInterface $creatorIndex
     *
     * @return string
     */
    private function getName(IndexInterface $creatorIndex): string
    {
        return $creatorIndex->getName() ?? '';
    }

    /**
     * @param IndexInterface $creatorIndex
     *
     * @return string
     */
    public function getKeyName(IndexInterface $creatorIndex): string
    {
        if ($creatorIndex instanceof PrimaryKeyInterface) {
            return 'PRIMARY';
        }
        $keyName = $this->getName($creatorIndex);

        return $keyName ?? trim(str_replace('KEY', '', $creatorIndex->getType()));
    }

    /**
     * @param string $tableName
     *
     * @throws LineGeneratorException
     *
     * @return array
     */
    public function fetchIndexesFromDb(string $tableName): array
    {
        try {
            $indexes = $this->db->fetchAll('SHOW INDEXES FROM ' . $this->db->escapeIdentifier($tableName));
        } catch (QueryFailureException | EscapingException $exception) {
            throw new LineGeneratorException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getPrevious()
            );
        }

        $result = [];
        foreach ($indexes as $index) {
            $keyName = $index['Key_name'];
            $resultKey = array_search($keyName, array_column($result, 'name'), true);

            if ($resultKey !== false) {
                $result[$resultKey]['columns'][] = $index['Column_name'];
            }

            if ($resultKey === false) {
                $result[] = [
                    'name'    => $index['Key_name'],
                    'columns' => [$index['Column_name']],
                    'unique'  => (int)$index['Non_unique'] === 0,
                ];
            }
        }

        return $result;
    }

    /**
     * @param string $name
     *
     * @throws EscapingException
     *
     * @return string
     */
    public function escape(string $name): string
    {
        return $this->db->escapeIdentifier($name);
    }
}
