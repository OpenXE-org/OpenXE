<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleApi\Data;

use Countable;

final class GoogleAccountPropertyCollection implements Countable
{
    /** @var GoogleAccountPropertyValue[] $properties */
    private $properties;

    /**
     * @param GoogleAccountPropertyValue[] $properties
     */
    public function __construct(array $properties = [])
    {
        $this->properties = [];
        foreach ($properties as $property) {
            $this->add($property);
        }
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->properties) && $this->properties[$key] !== null;
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    public function get(string $key): ?string
    {
        if (!array_key_exists($key, $this->properties) || $this->properties[$key] === null) {
            return null;
        }

        return $this->properties[$key]->getValue();
    }

    /**
     * Sets property immutable
     *
     * @param string $key
     * @param string $value
     *
     * @return GoogleAccountPropertyCollection
     */
    public function set(string $key, string $value): GoogleAccountPropertyCollection
    {
        if ($this->has($key)) {
            $property = new GoogleAccountPropertyValue(
                $this->properties[$key]->getId(),
                $this->properties[$key]->getAccountId(),
                $this->properties[$key]->getKey(),
                $value
            );
        } else {
            $property = new GoogleAccountPropertyValue(null, null, $key, $value);
        }
        $properties = clone($this);
        $properties->properties[$key] = $property;

        return $properties;
    }

    /**
     * Removes property immutable
     *
     * @param string $key
     *
     * @return GoogleAccountPropertyCollection
     */
    public function remove(string $key): GoogleAccountPropertyCollection
    {
        if (!array_key_exists($key, $this->properties)) {
            return $this;
        }
        $properties = clone($this);
        $properties->properties[$key] = null;

        return $properties;
    }

    /**
     * @return GoogleAccountPropertyValue[]
     */
    public function getAll(): array
    {
        return $this->properties;
    }

    /**
     * Gets properties as Key => Value array
     *
     * @return array [key => value]
     */
    public function getkeyValueMap(): array
    {
        $array = [];
        foreach ($this->properties as $key => $obj) {
            if ($obj !== null) {
                $array[$key] = $obj->getValue();
            }
        }

        return $array;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        $count = 0;
        foreach ($this->properties as $property) {
            if ($property !== null) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @return array|null
     *
     * @codeCoverageIgnore
     */
    public function __debugInfo(): ?array
    {
        return $this->getkeyValueMap();
    }

    /**
     * @param GoogleAccountPropertyValue $property
     */
    private function add(GoogleAccountPropertyValue $property): void
    {
        $this->properties[$property->getKey()] = $property;
    }
}
