<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Index;

use Xentral\Components\SchemaCreator\Interfaces\IndexInterface;

final class Index implements IndexInterface
{
    /** @var string $type */
    private $type = 'INDEX';

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
            return 'index_'.implode('_', array_map('strtolower', $this->references));
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
        return false;
    }
}
