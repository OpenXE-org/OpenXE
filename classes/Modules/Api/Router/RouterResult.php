<?php

namespace Xentral\Modules\Api\Router;

class RouterResult
{
    /** @var string $resourceClass */
    protected $resourceClass;

    /** @var string $controllerClass */
    protected $controllerClass;

    /** @var string $controllerAction */
    protected $controllerAction;

    /** @var array $params */
    protected $params;

    /** @var string|null $params */
    protected $permission;

    /**
     * @param string      $version
     * @param string      $resource
     * @param string      $controller
     * @param string      $action
     * @param array       $params
     * @param string|null $permission
     */
    public function __construct($version, $resource, $controller, $action, array $params = array(), ?string $permission = null)
    {
        $this->resourceClass = sprintf('Xentral\Modules\Api\Resource\%sResource', $resource);
        $this->controllerClass = sprintf('Xentral\Modules\Api\Controller\%s\%sController', $version, $controller);
        $this->controllerAction = $action;
        $this->params = $params;
        $this->permission = $permission;
    }

    /**
     * @return string
     */
    public function getResourceClass()
    {
        return $this->resourceClass;
    }

    /**
     * @return string
     */
    public function getControllerClass()
    {
        return $this->controllerClass;
    }

    /**
     * @return string
     */
    public function getControllerAction()
    {
        return $this->controllerAction;
    }

    /**
     * @return array
     */
    public function getRouterParams()
    {
        return $this->params;
    }

    /**
     * @return string|null
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'controllerClass'  => $this->getControllerClass(),
            'controllerAction' => $this->getControllerAction(),
            'resourceClass'    => $this->getResourceClass(),
            'routerParams'     => $this->getRouterParams(),
            'permission'       => $this->getPermission(),
        ];
    }
}
