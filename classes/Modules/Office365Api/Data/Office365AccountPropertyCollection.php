<?php

declare(strict_types=1);

namespace Xentral\Modules\Office365Api\Data;

final class Office365AccountPropertyCollection
{
    /** @var Office365AccountPropertyValue[] */
    private $properties = [];

    public function __construct(array $properties = [])
    {
        foreach ($properties as $property) {
            if ($property instanceof Office365AccountPropertyValue) {
                $this->properties[$property->getName()] = $property;
            }
        }
    }

    public function add(Office365AccountPropertyValue $property): void
    {
        $this->properties[$property->getName()] = $property;
    }

    public function get(string $name): ?string
    {
        return $this->properties[$name]?->getValue();
    }

    public function has(string $name): bool
    {
        return isset($this->properties[$name]);
    }

    public function all(): array
    {
        return array_map(
            fn(Office365AccountPropertyValue $prop) => $prop->getValue(),
            $this->properties
        );
    }

    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->properties);
    }

    public function count(): int
    {
        return count($this->properties);
    }
}
