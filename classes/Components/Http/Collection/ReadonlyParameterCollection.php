<?php

namespace Xentral\Components\Http\Collection;

class ReadonlyParameterCollection
{
    /** @var array $params */
    protected $params;

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = [];
        foreach ($params as $name => $value) {
            $this->params[$name] = $value;
        }
    }

    /**
     * Returns all parameters and values as associative array
     *
     * @return array
     */
    public function all()
    {
        return $this->params;
    }

    /**
     * Returns true if parameter is available
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->params);
    }

    /**
     * Returns the parameter value
     *
     * @param string     $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return isset($this->params[$name]) ? $this->params[$name] : $default;
    }

    /**
     * Returns the parameter value casted to boolean
     *
     * @param string $name
     * @param bool   $default
     *
     * @return bool Returns true for "1", "true", "on" and "yes"; otherwise false
     */
    public function getBool($name, $default = false)
    {
        return filter_var($this->get($name, $default), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Returns the parameter value casted to integer
     *
     * @param string $name
     * @param int    $default
     *
     * @return int
     */
    public function getInt($name, $default = 0)
    {
        return (int)$this->get($name, $default);
    }

    /**
     * Returns a string with digits only (0-9)
     *
     * @param string $name
     * @param string $default
     *
     * @return string
     */
    public function getDigits($name, $default = '')
    {
        return (string)preg_replace('#[^0-9]#', '', $this->get($name, $default));
    }

    /**
     * Returns a string with alphabetic characters only (A-Z and a-z)
     *
     * @param string $name
     * @param string $default
     *
     * @return string
     */
    public function getAlpha($name, $default = '')
    {
        return (string)preg_replace('#[^A-Za-z]#', '', $this->get($name, $default));
    }

    /**
     * Returns a string with alphanumeric characters only (A-Z, a-z and 0-9)
     *
     * @param string $name
     * @param string $default
     *
     * @return string
     */
    public function getAlphaNum($name, $default = '')
    {
        return (string)preg_replace('#[^A-Za-z0-9]#', '', $this->get($name, $default));
    }

    /**
     * Returns a string with alphanumeric characters and dashes only (A-Z, a-z, 0-9, Minus and Underscore)
     *
     * @param string $name
     * @param string $default
     *
     * @return string
     */
    public function getAlphaNumWithDashes($name, $default = '')
    {
        return (string)preg_replace('#[^A-Za-z0-9_-]#', '', $this->get($name, $default));
    }
}
