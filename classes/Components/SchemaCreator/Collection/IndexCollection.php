<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Collection;

use ArrayIterator;
use IteratorAggregate;
use Xentral\Components\SchemaCreator\Exception\SchemaCreatorInvalidArgumentException;
use Xentral\Components\SchemaCreator\Interfaces\PrimaryKeyInterface;
use Xentral\Components\SchemaCreator\Interfaces\IndexInterface;

final class IndexCollection implements IteratorAggregate
{

    /** @var array|IndexInterface[]  */
    private $values = [];

    /**
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        foreach ($values as $value) {
            $this->add($value);
        }
    }

    /**
     * @return bool
     */
    public function hasPrimaryKey(): bool
    {
        foreach ($this->values as $configuredKeys) {
            if ($configuredKeys instanceof PrimaryKeyInterface) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasIndex(string $key): bool
    {
        /** @var IndexInterface $configuredKeys */
        foreach ($this->values as $configuredKeys) {
            if ($key === $configuredKeys->getName()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param IndexInterface $key
     *
     * @return void
     */
    public function add(IndexInterface $key): void
    {
        if ($this->hasIndex($key->getName()) === true) {
            throw new SchemaCreatorInvalidArgumentException(
                sprintf('Key name `%s` already added', $key->getName())
            );
        }

        $this->values[] = $key;
    }

    /**
     * @return array
     */
    public function getIndexNames(): array
    {
        $indexes = [];
        foreach ($this->values as $index) {
            $indexes[] = $index instanceof PrimaryKeyInterface ? 'PRIMARY' : $index->getName();
        }

        return $indexes;
    }

    /**
     * @return ArrayIterator|IndexInterface[]
     */
    public function getIterator()
    {
        return new ArrayIterator($this->values);
    }
}
