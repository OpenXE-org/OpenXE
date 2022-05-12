<?php

namespace Xentral\Core\DependencyInjection;

interface ContainerInterface
{
    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name);

    /**
     * @param string $name
     *
     * @return mixed|object
     */
    public function get($name);
}
