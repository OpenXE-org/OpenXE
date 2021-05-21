<?php

namespace Xentral\Components\Http\Collection;

class ParameterCollection extends ReadonlyParameterCollection
{
    /**
     * Sets a parameter; Existing parameter value will be overwritten
     *
     * @param string $name
     * @param mixed  $value
     */
    public function set($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * Sets multiple parameters; Existing parameters will be overwritten
     *
     * @param array $values
     */
    public function add(array $values)
    {
        $this->params = array_merge($this->params, $values);
    }

    /**
     * Removes a parameter
     *
     * @param string $name
     */
    public function remove($name)
    {
        unset($this->params[$name]);
    }
}
