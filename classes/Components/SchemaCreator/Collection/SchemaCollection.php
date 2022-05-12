<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Collection;

use ArrayIterator;
use IteratorAggregate;
use Xentral\Components\SchemaCreator\Schema\TableSchema;

final class SchemaCollection implements IteratorAggregate
{
    /** @var array|TableSchema[] */
    private $values = [];

    /**
     * @param TableSchema $schema
     */
    public function add(TableSchema $schema): void
    {
        $this->values[] = $schema;
    }

    /**
     * @return ArrayIterator|TableSchema[]
     */
    public function getIterator()
    {
        return new ArrayIterator($this->values);
    }
}
