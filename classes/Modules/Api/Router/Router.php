<?php

namespace Xentral\Modules\Api\Router;

use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use FastRoute\Dispatcher\GroupCountBased as RouteDispatcher;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteCollector as RouteCollection;
use FastRoute\RouteParser\Std as RouteParser;
use Xentral\Modules\Api\Exception\MethodNotAllowedException;
use Xentral\Modules\Api\Exception\RouteNotFoundException;
use Xentral\Modules\Api\Exception\ServerErrorException;

class Router
{
    /** @var RouteCollection $routes */
    protected $routes;

    /**
     * @return RouteCollection
     */
    public function createCollection()
    {
        return new RouteCollector(new RouteParser(), new DataGenerator());
    }

    /**
     * @param RouteCollection $collection
     */
    public function setCollection($collection)
    {
        $this->routes = $collection;
    }

    /**
     * @param string $method
     * @param string $uri
     *
     * @return RouterResult
     */
    public function dispatch($method, $uri)
    {
        $dispatcher = new RouteDispatcher($this->routes->getData());

        $routeInfo = $dispatcher->dispatch($method, $uri);
        switch ($routeInfo[0]) {

            case Dispatcher::NOT_FOUND:
                throw new RouteNotFoundException();
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                throw new MethodNotAllowedException($allowedMethods);
                break;

            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $params = $routeInfo[2];

                list($version, $resource, $controller, $action, $permission) = $handler;

                return new RouterResult(
                    $version,
                    $resource,
                    $controller,
                    $action,
                    $params,
                    $permission
                );
        }

        throw new ServerErrorException();
    }
}
