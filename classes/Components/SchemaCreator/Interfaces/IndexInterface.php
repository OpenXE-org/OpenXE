<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Interfaces;

interface IndexInterface
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return array
     */
    public function getReferences(): array;

    /**
     * @return bool
     */
    public function isUnique(): bool;

}
