<?php

namespace Xentral\Core\DependencyInjection;

final class ServiceContainer extends AbstractBaseContainer
{
    /** @var ServiceRegistry $registry */
    private $registry;

    /**
     * @param ServiceRegistry $registry
     */
    public function __construct(ServiceRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return $this->registry->has($name);
    }

    /**
     * @param string $name
     *
     * @return mixed|object
     */
    public function get($name)
    {
        return $this->registry->get($name);
    }
}
