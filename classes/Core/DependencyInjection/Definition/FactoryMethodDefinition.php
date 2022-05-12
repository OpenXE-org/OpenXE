<?php

namespace Xentral\Core\DependencyInjection\Definition;

use Xentral\Core\DependencyInjection\Exception\InvalidArgumentException;

final class FactoryMethodDefinition
{
    /** @var callable $callable */
    private $callable;

    /** @var bool $shared Share the same instance? */
    private $shared;

    /**
     * @param callable $callable
     * @param bool     $shared
     *
     * @throws InvalidArgumentException
     */
    public function __construct($callable, $shared = true)
    {
        if (!is_callable($callable, false)) {
            throw new InvalidArgumentException(sprintf(
                'Definition can\'t be created. "%s::%s" is not callable.', $callable[0], $callable[1]
            ));
        }

        $this->callable = $callable;
        $this->shared = (bool)$shared;
    }

    /**
     * @return callable
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * @return bool
     */
    public function isShared()
    {
        return $this->shared;
    }
}
