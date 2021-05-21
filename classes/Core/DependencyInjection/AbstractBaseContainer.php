<?php

namespace Xentral\Core\DependencyInjection;

abstract class AbstractBaseContainer implements ContainerInterface
{
    /**
     * @param string $name
     *
     * @return bool
     */
    abstract public function has($name);

    /**
     * @param string $name
     *
     * @return object
     */
    abstract public function get($name);

    /**
     * @return void
     */
    public function __clone()
    {
    }

    /**
     * @return void
     */
    public function __wakeup()
    {
    }

    /**
     * @return void
     */
    public function __invoke()
    {
    }
}
