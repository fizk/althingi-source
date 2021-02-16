<?php

namespace Althingi\Router;

use Psr\Http\Message\RequestInterface;
use Laminas\Stdlib\ArrayUtils;
use Traversable;

class SimpleRouteStack implements RouteStackInterface
{
    protected PriorityList $routes;
    protected array $defaultParams = [];

    public function __construct()
    {
        $this->routes = new PriorityList();
    }

    /**
     * @param  array|Traversable $options
     */
    public static function factory($options = []): self
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (! is_array($options)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable set of options',
                __METHOD__
            ));
        }
        $instance = new static();

        if (isset($options['routes'])) {
            $instance->addRoutes($options['routes']);
        }

        if (isset($options['default_params'])) {
            $instance->setDefaultParams($options['default_params']);
        }

        return $instance;
    }

    public function addRoutes(/*array|\Traversable*/$routes): self
    {
        if (! is_array($routes) && ! $routes instanceof Traversable) {
            throw new Exception\InvalidArgumentException('addRoutes expects an array or Traversable set of routes');
        }

        foreach ($routes as $name => $route) {
            $this->addRoute($name, $route);
        }

        return $this;
    }

    public function addRoute(string $name, /*mixed*/ $route, int $priority = null): self
    {
        if (! $route instanceof RouteInterface) {
            $route = $this->routeFromArray($route);
        }

        if ($priority === null && isset($route->priority)) {
            $priority = $route->priority;
        }

        $this->routes->insert($name, $route, $priority);

        return $this;
    }

    public function removeRoute(string $name): self
    {
        $this->routes->remove($name);
        return $this;
    }

    public function setRoutes(/*array|\Traversable*/$routes): self
    {
        $this->routes->clear();
        $this->addRoutes($routes);
        return $this;
    }

    public function getRoutes(): Traversable
    {
        return $this->routes;
    }

    public function hasRoute(string $name): bool
    {
        return $this->routes->get($name) !== null;
    }

    public function getRoute(string $name): RouteInterface
    {
        return $this->routes->get($name);
    }

    public function setDefaultParams(array $params): self
    {
        $this->defaultParams = $params;
        return $this;
    }

    public function setDefaultParam(string $name, /*mixed*/$value): self
    {
        $this->defaultParams[$name] = $value;
        return $this;
    }

    protected function routeFromArray(/*array|Traversable*/$specs): RouteInterface
    {
        if ($specs instanceof Traversable) {
            $specs = ArrayUtils::iteratorToArray($specs);
        }

        if (! is_array($specs)) {
            throw new Exception\InvalidArgumentException('Route definition must be an array or Traversable object');
        }

        if (! isset($specs['type'])) {
            throw new Exception\InvalidArgumentException('Missing "type" option');
        }

        if (! isset($specs['options'])) {
            $specs['options'] = [];
        }

        $route = $specs['type']::factory($specs['options']);

        // $route = $this->getRoutePluginManager()->get($specs['type'], $specs['options']);

        // if (isset($specs['priority'])) {
        //     $route->priority = $specs['priority'];
        // }

        return $route;
    }

    public function match(RequestInterface $request, $pathOffset = null, array $options = [])
    {
        foreach ($this->routes as $name => $route) {
            if (($match = $route->match($request)) instanceof RouteMatch) {
                $match->setMatchedRouteName($name);

                foreach ($this->defaultParams as $paramName => $value) {
                    if ($match->getParam($paramName) === null) {
                        $match->setParam($paramName, $value);
                    }
                }

                return $match;
            }
        }

        return;
    }

    public function assemble(array $params = [], array $options = []) /*:mixed*/
    {
        if (! isset($options['name'])) {
            throw new Exception\InvalidArgumentException('Missing "name" option');
        }

        $route = $this->routes->get($options['name']);

        if (! $route) {
            throw new Exception\RuntimeException(sprintf('Route with name "%s" not found', $options['name']));
        }

        unset($options['name']);

        return $route->assemble(array_merge($this->defaultParams, $params), $options);
    }
}
