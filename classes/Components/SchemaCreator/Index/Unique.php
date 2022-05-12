<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Index;

use Xentral\Components\SchemaCreator\Interfaces\IndexInterface;
use Xentral\Components\SchemaCreator\Interfaces\UniqueIndexInterface;

final class Unique implements IndexInterface, UniqueIndexInterface
{
    /** @var string $type */
    private $type = UniqueIndexInterface::TYPE;

    /** @var string|null $name */
    private $name;

    /** @var array $references */
    private $references;

    /**
     * @param array       $references
     * @param string|null $name
     */
    public function __construct(array $references, ?string $name = null)
    {
        $this->name = $name;
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
        if (empty($this->name)) {
            return 'unique_'.implode('_', array_map('strtolower', $this->references));
        }

        return $this->name;
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
        return true;
    }
}
