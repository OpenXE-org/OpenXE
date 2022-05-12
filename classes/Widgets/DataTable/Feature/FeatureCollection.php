<?php

namespace Xentral\Widgets\DataTable\Feature;

use ArrayIterator;
use IteratorAggregate;
use Traversable;
use Xentral\Widgets\DataTable\Exception\FeatureExistsException;
use Xentral\Widgets\DataTable\Exception\FeatureNotFoundException;
use Xentral\Widgets\DataTable\Exception\InvalidArgumentException;

class FeatureCollection implements IteratorAggregate
{
    /** @var array $features */
    protected $features = [];

    /**
     * @param array|DataTableFeatureInterface[] $features
     */
    public function __construct(array $features = [])
    {
        foreach ($features as $feature) {
            $this->add($feature);
        }
    }

    /**
     * @param string $className Full-qualified class name
     *
     * @return bool
     */
    public function has($className)
    {
        $this->ensureClassNameParameter($className, __METHOD__);

        foreach ($this->features as $feature) {
            if (get_class($feature) === $className) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $className Full-qualified class name
     *
     * @throws FeatureNotFoundException
     *
     * @return DataTableFeatureInterface
     */
    public function get($className)
    {
        $this->ensureClassNameParameter($className, __METHOD__);

        foreach ($this->features as $feature) {
            if (get_class($feature) === $className) {
                return $feature;
            }
        }

        throw new FeatureNotFoundException(sprintf('Feature class "%s" not found.', $className));
    }

    /**
     * @return array|DataTableFeatureInterface[]
     */
    public function all()
    {
        return $this->features;
    }

    /**
     * Adds a new feature
     *
     * @param DataTableFeatureInterface $feature
     *
     * @throws FeatureExistsException If feature with same type already exists
     *
     * @return void
     */
    public function add(DataTableFeatureInterface $feature)
    {
        if ($this->has(get_class($feature))) {
            throw new FeatureExistsException(sprintf('Feature class "%s" already exists', get_class($feature)));
        }

        $this->features[] = $feature;
    }

    /**
     * Sets a feature; If feature with same type exists, it will be overwritten.
     *
     * @param DataTableFeatureInterface $feature
     *
     * @return void
     */
    public function set(DataTableFeatureInterface $feature)
    {
        $this->remove(get_class($feature));
        $this->features[] = $feature;
    }

    /**
     * @param string|object $className Full-qualified class name
     *
     * @return bool
     */
    public function remove($className)
    {
        $this->ensureClassNameParameter($className, __METHOD__);

        foreach ($this->features as $index => $feature) {
            if (get_class($feature) === $className) {
                unset($this->features[$index]);

                return true;
            }
        }

        return false;
    }

    /**
     * @return void
     */
    public function removeAll()
    {
        $this->features = [];
    }

    /**
     * @return ArrayIterator|Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->features);
    }

    /**
     * Deep copy object
     *
     * @return void
     */
    public function __clone()
    {
        foreach ($this->features as $index => $column) {
            $this->features[$index] = clone $column;
        }
    }

    /**
     * @param mixed  $className
     * @param string $callerName
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    protected function ensureClassNameParameter($className, $callerName)
    {
        if (!is_string($className)) {
            throw new InvalidArgumentException(sprintf(
                'Parameter "className" in method "%s" has to be a class name.', $callerName
            ));
        }

        if (!class_exists($className, true)) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is not a valid class.', $className
            ));
        }
    }
}
