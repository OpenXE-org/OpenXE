<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Index;

use Xentral\Components\SchemaCreator\Interfaces\IndexInterface;

final class Constraint implements IndexInterface
{
    /** @var string $type */
    private $type = 'CONSTRAINT';

    /** @var string $foreignKey */
    private $foreignKey;

    /** @var string $parentTable */
    private $parentTable;

    /** @var string $parentId */
    private $parentId;

    /** @var array $cascadeOn */
    private $cascadeOn;

    /** @var string $name */
    private $name;

    /**
     * @param string $name
     * @param array  $foreignKey
     * @param string $parentTable
     * @param array  $parentId
     * @param array  $cascadeOn
     */
    public function __construct(
        string $name,
        array $foreignKey,
        string $parentTable,
        array $parentId,
        array $cascadeOn = []
    ) {
        $this->parentId = $parentId;
        $this->foreignKey = $foreignKey;
        $this->cascadeOn = $cascadeOn;
        $this->parentTable = $parentTable;

        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getParenId(): array
    {
        return $this->parentId;
    }

    /**
     * @return string
     */
    public function getParentTable(): string
    {
        return $this->parentTable;
    }

    /**
     * @return array
     */
    public function getCascadeOn(): array
    {
        return $this->cascadeOn;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getForeignKey(): array
    {
        return $this->foreignKey;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getReferences(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function isUnique(): bool
    {
        return false;
    }
}
