<?php

namespace Xentral\Modules\Api\Http;

/**
 * @deprecated Use Xentral\Components\Http instead
 */
class ParameterCollection
{
    /** @var array $params */
    protected $params;

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->params;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->params);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function get($name)
    {
        return isset($this->params[$name]) ? $this->params[$name] : null;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function set($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * @param array $values
     */
    public function add(array $values)
    {
        $this->params = array_merge($this->params, $values);
    }

    /**
     * @param string $name
     */
    public function remove($name)
    {
        unset($this->params[$name]);
    }

    /**
     * @param string $name
     * @param int    $filter
     * @param array  $options
     *
     * @return mixed
     */
    public function filter($name, $filter = FILTER_DEFAULT, $options = [])
    {
        $value = $this->get($name);

        if (!is_array($options) && !empty($options)) {
            $options = array('flags' => $options);
        }

        return filter_var($value, $filter, $options);
    }

    /**
     * @param $name
     *
     * @return bool Gibt true zurück für  "1", "true", "on" und "yes"; sonst false
     */
    public function getBool($name)
    {
        return $this->filter($this->get($name), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param string $name
     *
     * @return int
     */
    public function getInt($name)
    {
        return (int)$this->get($name);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getAlpha($name)
    {
        return (string)preg_replace('#[^A-Za-z]#', '', $this->get($name));
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getAlphaNum($name)
    {
        return (string)preg_replace('#[^A-Za-z0-9]#', '', $this->get($name));
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getDigits($name)
    {
        return (string)preg_replace('#[^0-9]#', '', $this->get($name));
    }
}
