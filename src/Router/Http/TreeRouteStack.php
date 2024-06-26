<?php

namespace Althingi\Router\Http;

use Psr\Http\Message\RequestInterface;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Diactoros\Uri;
use Althingi\Router\Exception;
use Althingi\Router\SimpleRouteStack;
use Althingi\Router\Http\Part;
use Traversable;
use ArrayObject;

class TreeRouteStack extends SimpleRouteStack
{
    protected ?string $baseUrl = null;

    protected ?Uri $requestUri = null;

    protected ArrayObject $prototypes;

    public function __construct()
    {
        $this->prototypes = new ArrayObject();
        parent::__construct();
    }

    public static function factory(/*array|Traversable*/$options = []): static
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable set of options',
                __METHOD__
            ));
        }

        return parent::factory($options);
    }

    public function addRoute(string $name, /*mixed*/$route, int $priority = null): static
    {
        if (!$route instanceof RouteInterface) {
            $route = $this->routeFromArray($route);
        }

        return parent::addRoute($name, $route, $priority);
    }

    protected function routeFromArray(/*string|array|Traversable*/$specs): RouteInterface
    {
        if (is_string($specs)) {
            if (null === ($route = $this->getPrototype($specs))) {
                throw new Exception\RuntimeException(sprintf('Could not find prototype with name %s', $specs));
            }

            return $route;
        } elseif ($specs instanceof Traversable) {
            $specs = ArrayUtils::iteratorToArray($specs);
        } elseif (!is_array($specs)) {
            throw new Exception\InvalidArgumentException('Route definition must be an array or Traversable object');
        }

        if (isset($specs['chain_routes'])) {
            if (!is_array($specs['chain_routes'])) {
                throw new Exception\InvalidArgumentException('Chain routes must be an array or Traversable object');
            }

            $chainRoutes = array_merge([$specs], $specs['chain_routes']);
            unset($chainRoutes[0]['chain_routes']);

            if (isset($specs['child_routes'])) {
                unset($chainRoutes[0]['child_routes']);
            }

            $options = [
                'routes'        => $chainRoutes,
                'route_plugins' => $this->routePluginManager,
                'prototypes'    => $this->prototypes,
            ];

            $route = $this->routePluginManager->get('chain', $options);
        } else {
            $route = parent::routeFromArray($specs);
        }

        if (!$route instanceof RouteInterface) {
            throw new Exception\RuntimeException('Given route does not implement HTTP route interface');
        }

        if (isset($specs['child_routes'])) {
            $options = [
                'route'         => $route,
                'may_terminate' => (isset($specs['may_terminate']) && $specs['may_terminate']),
                'child_routes'  => $specs['child_routes'],
                // 'route_plugins' => $this->routePluginManager,
                'prototypes'    => $this->prototypes,
            ];

            $priority = (isset($route->priority) ? $route->priority : null);

            // $route = $this->routePluginManager->get('part', $options);
            $route = Part::factory($options);
            $route->priority = $priority;
        }

        return $route;
    }

    public function addPrototypes(Traversable $routes): static
    {
        if (!is_array($routes) && !$routes instanceof Traversable) {
            throw new Exception\InvalidArgumentException('addPrototypes expects an array or Traversable set of routes');
        }

        foreach ($routes as $name => $route) {
            $this->addPrototype($name, $route);
        }

        return $this;
    }

    public function addPrototype(string $name, /*mixed*/$route): static
    {
        if (!$route instanceof RouteInterface) {
            $route = $this->routeFromArray($route);
        }

        $this->prototypes[$name] = $route;

        return $this;
    }

    public function getPrototype(string $name) /*: RouteInterface|null */
    {
        if (isset($this->prototypes[$name])) {
            return $this->prototypes[$name];
        }

        return null;
    }

    public function match(
        RequestInterface $request, /*int*/
        $pathOffset = null,
        array $options = []
    ): ?RouteMatch/* : RouteMatch|null*/ {


        // if ($this->baseUrl === null && method_exists($request, 'getBaseUrl')) {
        //     $this->setBaseUrl($request->getBaseUrl());
        // }

        $uri           = $request->getUri();
        $baseUrlLength = $this->baseUrl ? (strlen($this->baseUrl) ?: null) : null;

        if ($pathOffset !== null) {
            $baseUrlLength += $pathOffset;
        }

        if ($this->requestUri === null) {
            $this->setRequestUri($uri);
        }

        if ($baseUrlLength !== null) {
            $pathLength = strlen($uri->getPath()) - $baseUrlLength;
        } else {
            $pathLength = null;
        }

        foreach ($this->routes as $name => $route) {
            if (
                ($match = $route->match($request, $baseUrlLength, $options)) instanceof RouteMatch
                && ($pathLength === null || $match->getLength() === $pathLength)
            ) {
                $match->setMatchedRouteName($name);

                foreach ($this->defaultParams as $paramName => $value) {
                    if ($match->getParam($paramName) === null) {
                        $match->setParam($paramName, $value);
                    }
                }

                return $match;
            }
        }

        return null;
    }

    public function assemble(array $params = [], array $options = [])/* : mixed*/
    {
        if (!isset($options['name'])) {
            throw new Exception\InvalidArgumentException('Missing "name" option');
        }

        $names = explode('/', $options['name'], 2);
        $route = $this->routes->get($names[0]);

        if (!$route) {
            throw new Exception\RuntimeException(sprintf('Route with name "%s" not found', $names[0]));
        }

        if (isset($names[1])) {
            if (!$route instanceof TreeRouteStack) {
                throw new Exception\RuntimeException(sprintf(
                    'Route with name "%s" does not have child routes',
                    $names[0]
                ));
            }
            $options['name'] = $names[1];
        } else {
            unset($options['name']);
        }

        if (isset($options['only_return_path']) && $options['only_return_path']) {
            return $this->baseUrl . $route->assemble(array_merge($this->defaultParams, $params), $options);
        }

        if (!isset($options['uri'])) {
            $uri = new Uri();

            if (isset($options['force_canonical']) && $options['force_canonical']) {
                if ($this->requestUri === null) {
                    throw new Exception\RuntimeException('Request URI has not been set');
                }

                $uri->withScheme($this->requestUri->getScheme())
                    ->withPort($this->requestUri->getPort())
                    ->withHost($this->requestUri->getHost());
            }

            $options['uri'] = $uri;
        } else {
            /** @var $uri  \Laminas\Diactoros\Uri */
            $uri = $options['uri'];
        }

        $path = $this->baseUrl . $route->assemble(array_merge($this->defaultParams, $params), $options);

        if (isset($options['query'])) {
            $uri = $uri->withQuery($options['query']);
        }

        if (isset($options['fragment'])) {
            $uri = $uri->withFragment($options['fragment']);
        }

        if (
            (isset($options['force_canonical'])
                && $options['force_canonical'])
            || $uri->getHost() !== null
            || $uri->getScheme() !== null
        ) {
            if (($uri->getHost() === null || $uri->getScheme() === null) && $this->requestUri === null) {
                throw new Exception\RuntimeException('Request URI has not been set');
            }

            if ($uri->getHost() === null) {
                $uri = $uri->withHost($this->requestUri->getHost());
            }

            if ($uri->getScheme() === null) {
                $uri = $uri->withScheme($this->requestUri->getScheme());
            }

            $uri = $uri->withPath($path);

            // if (!isset($options['normalize_path']) || $options['normalize_path']) {
            //     $uri->normalize();
            // }

            return $uri->__toString();
        }
        // elseif (!$uri->isAbsolute() && $uri->isValidRelative()) {
        //     $uri->setPath($path);

        //     if (!isset($options['normalize_path']) || $options['normalize_path']) {
        //         $uri->normalize();
        //     }

        //     return $uri->toString();
        // }

        return $path;
    }

    public function setBaseUrl(string $baseUrl): static
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        return $this;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function setRequestUri(Uri $uri): static
    {
        $this->requestUri = $uri;
        return $this;
    }

    public function getRequestUri(): Uri
    {
        return $this->requestUri;
    }
}
