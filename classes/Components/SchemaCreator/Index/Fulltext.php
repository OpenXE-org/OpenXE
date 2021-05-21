<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Index;

use Xentral\Components\SchemaCreator\Interfaces\IndexInterface;

final class Fulltext implements IndexInterface
{
    /** @var string $type */
    private $type = 'FULLTEXT';

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

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        if (empty($this->name)) {
            return 'fulltextkey_'.implode('_', array_map('strtolower', $this->references));
        }

        return $this->name;
    }

    public function getReferences(): array
    {
        return $this->references;
    }

    public function isUnique(): bool
    {
        return false;
    }
}
