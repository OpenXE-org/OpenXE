<?php

namespace Xentral\Core\DependencyInjection;

use Xentral\Components\Logger\LoggerAwareTrait;
use Xentral\Core\DependencyInjection\Definition\FactoryMethodDefinition;
use Xentral\Core\DependencyInjection\Exception\InvalidArgumentException;
use Xentral\Core\DependencyInjection\Exception\ServiceNotFoundException;

final class ServiceRegistry extends AbstractBaseContainer
{
    /** @var array $services Storage for service instances */
    private $services = [];

    /** @var FactoryMethodDefinition[] $factories Storage for factory methods */
    private $factories = [];

    /**
     * @param array $factoryMethods
     */
    public function __construct(array $factoryMethods = [])
    {
        $this->addFactories($factoryMethods);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return $this->hasService($name) || $this->hasFactory($name);
    }

    /**
     * @param string $name
     *
     * @throws ServiceNotFoundException
     *
     * @return object|mixed
     */
    public function get($name)
    {
        if ($this->hasService($name)) {
            return $this->services[$name];
        }

        if ($this->hasFactory($name)) {
            $definition = $this->factories[$name];
            $factoryMethod = $definition->getCallable();
            $serviceInstance = $factoryMethod($this->get('ServiceContainer'));
            //inject Logger if required
            if (
                $this->has('Logger')
                && in_array(LoggerAwareTrait::class, class_uses($serviceInstance), true)
            ) {
                /** @var LoggerAwareTrait $serviceInstance */
                $serviceInstance->setLogger($this->get('Logger'));
            }

            // Don't save non-shared services
            if (!$definition->isShared()) {
                return $serviceInstance;
            }

            // Save shared services
            $this->add($name, $serviceInstance);

            return $this->services[$name];
        }

        throw new ServiceNotFoundException(sprintf(
            'Service "%s" was not found.', $name
        ));
    }

    /**
     * @param string $name
     * @param object $instance
     *
     * @throws InvalidArgumentException
     */
    public function add($name, $instance)
    {
        if (!is_object($instance)) {
            throw new InvalidArgumentException(sprintf(
                '%s could not be added. Only objects can be added to container.', $name
            ));
        }

        $this->services[$name] = $instance;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasFactory($name)
    {
        return isset($this->factories[$name]);
    }

    /**
     * @param string   $name
     * @param callable $callable
     * @param bool     $shared
     *
     * @throws InvalidArgumentException
     */
    public function addFactory($name, $callable, $shared = true)
    {
        if (!is_callable($callable, true)) {
            throw new InvalidArgumentException(sprintf(
                'Factory "%s" can not be added. Second argument must be a callable.', $name
            ));
        }

        if (!is_string($name)) {
            throw new InvalidArgumentException(sprintf(
                'Factory "%s" can not be added. Factory name must be a string.', $name
            ));
        }

        if ($this->hasFactory($name)) {
            throw new InvalidArgumentException(sprintf(
                'Factory "%s" can not be added. Factory is already present.', $name
            ));
        }

        $this->factories[$name] = new FactoryMethodDefinition($callable, $shared);
    }

    /**
     * @param array|\Iterator $factories
     */
    public function addFactories($factories)
    {
        foreach ($factories as $name => $callable) {
            $this->addFactory($name, $callable, true);
        }
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function hasService($name)
    {
        return isset($this->services[$name]);
    }
}
