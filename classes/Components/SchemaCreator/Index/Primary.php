<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Index;

use Xentral\Components\SchemaCreator\Interfaces\PrimaryKeyInterface;
use Xentral\Components\SchemaCreator\Interfaces\IndexInterface;

final class Primary implements IndexInterface, PrimaryKeyInterface
{

    /** @var string $type */
    private $type = 'PRIMARY KEY';

    /** @var array $references */
    private $references;

    /**
     * @param array $references
     */
    public function __construct(array $references)
    {
        $this->references = $references;
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'PRIMARY';
    }

    /**
     * @inheritDoc
     */
    public function getReferences(): array
    {
        return $this->references;
    }

    /**
     * @inheritDoc
     */
    public function isUnique(): bool
    {
        return false;
    }
}
